   {{-- <div class="container-xxl flex-grow-1 container-p-y">
       <div class="row">
           <div class="col-md-12">
               <div class="nav-align-top   ">
                   <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-md-0 gap-2">
                       <li class="nav-item ">
                           <a class="nav-link {{ request()->segment(4) == 'dashboard' ? 'active' : '' }}"
                               href="{{ route('my-society.conference.dashboard', $conference) }}"><i 
                                   class="icon-base ti tabler-users icon-sm me-1_5"></i>Dashboard</a>
                       </li>
                       <li class="nav-item"> 
                           <a class="nav-link  {{ request()->segment(4) == 'conference-registration' ? 'active' : '' }} "
                               href="{{ checkRegistration($conference) ? route('my-society.conference.index', $conference) : route('my-society.conference.create', $conference) }}"><i
                                   class="icon-base ti tabler-users icon-sm me-1_5"></i>Conference Registration</a>
                       </li>
                       <li class="nav-item">
                           <a class="nav-link  {{ request()->segment(4) == 'submission' ? 'active' : '' }}"
                               href="{{ route('my-society.conference.submission.index', $conference) }}"> 
                               <i class="icon-base ti tabler-license icon-sm me-1_5"></i> Submission</a>
                       </li>
                       <li class="nav-item">
                           <a class="nav-link {{ request()->segment(4) == 'workshop-registration' ? 'active' : '' }}" href="{{ route('my-society.conference.workshop.index', $conference) }}"><i
                                   class="icon-base ti tabler-bookmark icon-sm me-1_5"></i> Workshop Registration</a>
                       </li>
                   </ul>
               </div>

           </div>
       </div>
   </div> --}}
