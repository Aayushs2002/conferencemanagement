<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SentryMonitorController extends Controller
{
    protected $sentryApiUrl;
    protected $sentryAuthToken;
    protected $sentryOrgSlug;
    protected $sentryProjectSlug;

    public function __construct()
    {
        $this->sentryApiUrl = 'https://sentry.io/api/0';
        $this->sentryAuthToken = env('SENTRY_AUTH_TOKEN');
        $this->sentryOrgSlug = env('SENTRY_ORG_SLUG');
        $this->sentryProjectSlug = env('SENTRY_PROJECT_SLUG');
    }

    /**
     * Display the Sentry issues dashboard
     */
    public function index(Request $request)
    {
        try {
            $filter = $request->get('filter', 'unresolved');
            $page = $request->get('page', 1);
            
            // Fetch issues from Sentry API
            $issues = $this->fetchIssues($filter, $page);
            
            // Fetch project stats
            $stats = $this->fetchProjectStats();
            // dd($stats);
            // Cache unresolved count for sidebar badge
            if ($filter === 'unresolved') {
                Cache::put('sentry_unresolved_count', count($issues), 300);
            }
            
            return view('backend.sentry.index', compact('issues', 'stats', 'filter'));
        } catch (\Exception $e) {
            return view('backend.sentry.index', [
                'issues' => [],
                'stats' => [],
                'error' => 'Unable to connect to Sentry. Please check your configuration.',
                'filter' => $filter ?? 'unresolved'
            ]);
        }
    }

    /**
     * Fetch issues from Sentry API
     */
    protected function fetchIssues($filter = 'unresolved', $page = 1)
    {
        if (!$this->sentryAuthToken || !$this->sentryOrgSlug || !$this->sentryProjectSlug) {
            return [];
        }

        $cacheKey = "sentry_issues_{$filter}_{$page}";
        
        return Cache::remember($cacheKey, 300, function () use ($filter, $page) {
            $query = $filter === 'all' ? '' : "is:{$filter}";
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->sentryAuthToken,
            ])->get("{$this->sentryApiUrl}/projects/{$this->sentryOrgSlug}/{$this->sentryProjectSlug}/issues/", [
                'query' => $query,
                'statsPeriod' => '24h',
                'limit' => 50,
                'page' => $page,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        });
    }

    /**
     * Fetch project statistics
     */
    protected function fetchProjectStats()
    {
        if (!$this->sentryAuthToken || !$this->sentryOrgSlug || !$this->sentryProjectSlug) {
            return [];
        }

        $cacheKey = "sentry_project_stats";
        
        return Cache::remember($cacheKey, 300, function () {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->sentryAuthToken,
            ])->get("{$this->sentryApiUrl}/projects/{$this->sentryOrgSlug}/{$this->sentryProjectSlug}/");

            if ($response->successful()) {
                // dd($response->json());
                return $response->json();
            }

            return [];
        });
    }

    /**
     * Show issue details
     */
    public function show($issueId)
    {
        try {
            $issue = $this->fetchIssueDetails($issueId);
            $events = $this->fetchIssueEvents($issueId);
            
            return view('backend.sentry.show', compact('issue', 'events'));
        } catch (\Exception $e) {
            return redirect()->route('sentry.index')->with('error', 'Unable to fetch issue details.');
        }
    }

    /**
     * Fetch issue details
     */
    protected function fetchIssueDetails($issueId)
    {
        if (!$this->sentryAuthToken) {
            return [];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->sentryAuthToken,
        ])->get("{$this->sentryApiUrl}/issues/{$issueId}/");

        if ($response->successful()) {
            return $response->json();
        }

        return [];
    }

    /**
     * Fetch issue events
     */
    protected function fetchIssueEvents($issueId)
    {
        if (!$this->sentryAuthToken) {
            return [];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->sentryAuthToken,
        ])->get("{$this->sentryApiUrl}/issues/{$issueId}/events/", [
            'limit' => 10,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return [];
    }

    /**
     * Resolve an issue
     */
    public function resolve($issueId)
    {
        try {
            if (!$this->sentryAuthToken) {
                return response()->json(['success' => false, 'message' => 'Sentry not configured']);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->sentryAuthToken,
            ])->put("{$this->sentryApiUrl}/issues/{$issueId}/", [
                'status' => 'resolved',
            ]);

            if ($response->successful()) {
                Cache::forget("sentry_issues_unresolved_1");
                Cache::forget("sentry_issues_all_1");
                return response()->json(['success' => true, 'message' => 'Issue marked as resolved']);
            }

            return response()->json(['success' => false, 'message' => 'Failed to resolve issue']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Ignore an issue
     */
    public function ignore($issueId)
    {
        try {
            if (!$this->sentryAuthToken) {
                return response()->json(['success' => false, 'message' => 'Sentry not configured']);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->sentryAuthToken,
            ])->put("{$this->sentryApiUrl}/issues/{$issueId}/", [
                'status' => 'ignored',
            ]);

            if ($response->successful()) {
                Cache::forget("sentry_issues_unresolved_1");
                Cache::forget("sentry_issues_all_1");
                return response()->json(['success' => true, 'message' => 'Issue marked as ignored']);
            }

            return response()->json(['success' => false, 'message' => 'Failed to ignore issue']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Clear cache
     */
    public function clearCache()
    {
        Cache::flush();
        return redirect()->route('sentry.index')->with('success', 'Cache cleared successfully');
    }
}
