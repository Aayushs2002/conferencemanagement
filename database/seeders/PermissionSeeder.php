<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.  
     */
    public function run(): void
    {
        $permission = [
            //Conference Registration
            [
                'name' => 'View Conference Registration',
                'guard_name' => 'web',
                'parent' => 'Conference Registration'
            ],
            [
                'name' => 'Edit Conference Registration',
                'guard_name' => 'web',
                'parent' => 'Conference Registration'
            ],
            [
                'name' => 'Delete Conference Registration',
                'guard_name' => 'web',
                'parent' => 'Conference Registration'
            ],
            [
                'name' => 'Add People',
                'guard_name' => 'web',
                'parent' => 'Conference Registration'
            ],
            [
                'name' => 'Convert Registrant Type',
                'guard_name' => 'web',
                'parent' => 'Conference Registration'
            ],
            [
                'name' => 'Export',
                'guard_name' => 'web',
                'parent' => 'Conference Registration'
            ],
            [
                'name' => 'Registration And Invitation',
                'guard_name' => 'web',
                'parent' => 'Conference Registration'
            ],
            [
                'name' => 'Exceptional Case',
                'guard_name' => 'web',
                'parent' => 'Conference Registration'
            ],

            //Pass Setting
            [
                'name' => 'View Pass Setting',
                'guard_name' => 'web',
                'parent' => 'Conference Pass Setting'
            ],
            [
                'name' => 'Add Pass Setting',
                'guard_name' => 'web',
                'parent' => 'Conference Pass Setting'
            ],
            [
                'name' => 'Edit Pass Setting',
                'guard_name' => 'web',
                'parent' => 'Conference Pass Setting'
            ],

            //Certificate Setting
            [
                'name' => 'View Certificate Setting',
                'guard_name' => 'web',
                'parent' => 'Conference Certificate Setting'
            ],

            [
                'name' => 'Add Certificate Setting',
                'guard_name' => 'web',
                'parent' => 'Conference Certificate Setting'
            ],
            [
                'name' => 'Edit Certificate Setting',
                'guard_name' => 'web',
                'parent' => 'Conference Certificate Setting'
            ],

            //Submission
            [
                'name' => 'View Submission',
                'guard_name' => 'web',
                'parent' => 'Submission'
            ],
            [
                'name' => 'Edit Submission',
                'guard_name' => 'web',
                'parent' => 'Submission'
            ],
            [
                'name' => 'Delete Submission',
                'guard_name' => 'web',
                'parent' => 'Submission'
            ],
            [
                'name' => 'Convert Presentation Type',
                'guard_name' => 'web',
                'parent' => 'Submission'
            ],
            [
                'name' => 'Change Request Status',
                'guard_name' => 'web',
                'parent' => 'Submission'
            ],
            [
                'name' => 'Expert Assign',
                'guard_name' => 'web',
                'parent' => 'Submission'
            ],
            [
                'name' => 'View Score',
                'guard_name' => 'web',
                'parent' => 'Submission'
            ],
            [
                'name' => 'Send Mail',
                'guard_name' => 'web',
                'parent' => 'Submission'
            ],
            [
                'name' => 'View Author',
                'guard_name' => 'web',
                'parent' => 'Submission'
            ],
            [
                'name' => 'View Discussion',
                'guard_name' => 'web',
                'parent' => 'Submission'
            ],

            //submission setting
            [
                'name' => 'View Submission Setting',
                'guard_name' => 'web',
                'parent' => 'Submission Setting'
            ],
            [
                'name' => 'Add/Edit Submission Setting',
                'guard_name' => 'web',
                'parent' => 'Submission Setting'
            ],

            //Submission Category/major Track
            [
                'name' => 'View Category/Major Track',
                'guard_name' => 'web',
                'parent' => 'Category/Major Track'
            ],
            [
                'name' => 'Add Category/Major Track',
                'guard_name' => 'web',
                'parent' => 'Category/Major Track'
            ],
            [
                'name' => 'Edit Category/Major Track',
                'guard_name' => 'web',
                'parent' => 'Category/Major Track'
            ],
            [
                'name' => 'Delete Category/Major Track',
                'guard_name' => 'web',
                'parent' => 'Category/Major Track'
            ],
            //Scientific Session
            [
                'name' => 'View Scientific Session',
                'guard_name' => 'web',
                'parent' => 'Scientific Session'
            ],
            [
                'name' => 'Add Scientific Session',
                'guard_name' => 'web',
                'parent' => 'Scientific Session'
            ],
            [
                'name' => 'Edit Scientific Session',
                'guard_name' => 'web',
                'parent' => 'Scientific Session'
            ],
            [
                'name' => 'Delete Scientific Session',
                'guard_name' => 'web',
                'parent' => 'Scientific Session'
            ],
            [
                'name' => 'View Poll',
                'guard_name' => 'web',
                'parent' => 'Scientific Session'
            ],
            [
                'name' => 'Create Poll',
                'guard_name' => 'web',
                'parent' => 'Scientific Session'
            ],
            [
                'name' => 'Edit Poll',
                'guard_name' => 'web',
                'parent' => 'Scientific Session'
            ],
            [
                'name' => 'Delete Poll',
                'guard_name' => 'web',
                'parent' => 'Scientific Session'
            ],

            //Scientific Session Category
            [
                'name' => 'View Scientific Session Category',
                'guard_name' => 'web',
                'parent' => 'Scientific Session Category'
            ],
            [
                'name' => 'Add Scientific Session Category',
                'guard_name' => 'web',
                'parent' => 'Scientific Session Category'
            ],
            [
                'name' => 'Edit Scientific Session Category',
                'guard_name' => 'web',
                'parent' => 'Scientific Session Category'
            ],
            [
                'name' => 'Delete Scientific Session Category',
                'guard_name' => 'web',
                'parent' => 'Scientific Session Category'
            ],

            //Scientific Session Hall
            [
                'name' => 'View Scientific Session Hall',
                'guard_name' => 'web',
                'parent' => 'Scientific Session Hall'
            ],
            [
                'name' => 'Add Scientific Session Hall',
                'guard_name' => 'web',
                'parent' => 'Scientific Session Hall'
            ],
            [
                'name' => 'Edit Scientific Session Hall',
                'guard_name' => 'web',
                'parent' => 'Scientific Session Hall'
            ],
            [
                'name' => 'Delete Scientific Session Hall',
                'guard_name' => 'web',
                'parent' => 'Scientific Session Hall'
            ],

            //Workshop
            [
                'name' => 'View Workshop',
                'guard_name' => 'web',
                'parent' => 'Workshop'
            ],
            [
                'name' => 'Add Workshop',
                'guard_name' => 'web',
                'parent' => 'Workshop'
            ],
            [
                'name' => 'Edit Workshop',
                'guard_name' => 'web',
                'parent' => 'Workshop'
            ],
            [
                'name' => 'Delete Workshop',
                'guard_name' => 'web',
                'parent' => 'Workshop'
            ],
            [
                'name' => 'Add/Update Registration Price',
                'guard_name' => 'web',
                'parent' => 'Workshop'
            ],
            [
                'name' => 'Regster New User',
                'guard_name' => 'web',
                'parent' => 'Workshop'
            ],
            [
                'name' => 'Regster User in Exceptional Case',
                'guard_name' => 'web',
                'parent' => 'Workshop'
            ],

            //Workshop Trainer
            [
                'name' => 'View Workshop Trainer',
                'guard_name' => 'web',
                'parent' => 'Workshop Trainer'
            ],
            [
                'name' => 'Add Workshop Trainer',
                'guard_name' => 'web',
                'parent' => 'Workshop Trainer'
            ],
            [
                'name' => 'Edit Workshop Trainer',
                'guard_name' => 'web',
                'parent' => 'Workshop Trainer'
            ],
            [
                'name' => 'Delete Workshop Trainer',
                'guard_name' => 'web',
                'parent' => 'Workshop Trainer'
            ],

            //Workshop Registrant
            [
                'name' => 'View Workshop Registrant',
                'guard_name' => 'web',
                'parent' => 'Workshop Registrant'
            ],
            [
                'name' => 'Edit Workshop Registrant',
                'guard_name' => 'web',
                'parent' => 'Workshop Registrant'
            ],
            [
                'name' => 'Delete Workshop Registrant',
                'guard_name' => 'web',
                'parent' => 'Workshop Registrant'
            ],

            //Workshop Pass Setting
            [
                'name' => 'View Workshop Pass Setting',
                'guard_name' => 'web',
                'parent' => 'Workshop Pass Setting'
            ],
            [
                'name' => 'Add Workshop Pass Setting',
                'guard_name' => 'web',
                'parent' => 'Workshop Pass Setting'
            ],
            [
                'name' => 'Edit Workshop Pass Setting',
                'guard_name' => 'web',
                'parent' => 'Workshop Pass Setting'
            ],

            //Committee
            [
                'name' => 'View Committee',
                'guard_name' => 'web',
                'parent' => 'Committee'
            ],
            [
                'name' => 'Add Committee',
                'guard_name' => 'web',
                'parent' => 'Committee'
            ],
            [
                'name' => 'Edit Committee',
                'guard_name' => 'web',
                'parent' => 'Committee'
            ],
            [
                'name' => 'Delete Committee',
                'guard_name' => 'web',
                'parent' => 'Committee'
            ],

            //Committee Member
            [
                'name' => 'View Committee Member',
                'guard_name' => 'web',
                'parent' => 'Committee Member'
            ],
            [
                'name' => 'Add Committee Member',
                'guard_name' => 'web',
                'parent' => 'Committee Member'
            ],
            [
                'name' => 'Edit Committee Member',
                'guard_name' => 'web',
                'parent' => 'Committee Member'
            ],
            [
                'name' => 'Delete Committee Member',
                'guard_name' => 'web',
                'parent' => 'Committee Member'
            ],
            [
                'name' => 'Change Featured Committee Member',
                'guard_name' => 'web',
                'parent' => 'Committee Member'
            ],

            //Committee Designation
            [
                'name' => 'View Committee Designation',
                'guard_name' => 'web',
                'parent' => 'Committee Designation'
            ],
            [
                'name' => 'Add Committee Designation',
                'guard_name' => 'web',
                'parent' => 'Committee Designation'
            ],
            [
                'name' => 'Edit Committee Designation',
                'guard_name' => 'web',
                'parent' => 'Committee Designation'
            ],
            [
                'name' => 'Delete Committee Designation',
                'guard_name' => 'web',
                'parent' => 'Committee Designation'
            ],

            //Sponsor
            [
                'name' => 'View Sponsor',
                'guard_name' => 'web',
                'parent' => 'Sponsor'
            ],
            [
                'name' => 'Add Sponsor',
                'guard_name' => 'web',
                'parent' => 'Sponsor'
            ],
            [
                'name' => 'Edit Sponsor',
                'guard_name' => 'web',
                'parent' => 'Sponsor'
            ],
            [
                'name' => 'Delete Sponsor',
                'guard_name' => 'web',
                'parent' => 'Sponsor'
            ],
            [
                'name' => 'Change Publish Sponsor',
                'guard_name' => 'web',
                'parent' => 'Sponsor'
            ],
            [
                'name' => 'Add Participant Sponsor',
                'guard_name' => 'web',
                'parent' => 'Sponsor'
            ],

            //Sponsor Category
            [
                'name' => 'View Sponsor Category',
                'guard_name' => 'web',
                'parent' => 'Sponsor Category'
            ],
            [
                'name' => 'Add Sponsor Category',
                'guard_name' => 'web',
                'parent' => 'Sponsor Category'
            ],
            [
                'name' => 'Edit Sponsor Category',
                'guard_name' => 'web',
                'parent' => 'Sponsor Category'
            ],
            [
                'name' => 'Delete Sponsor Category',
                'guard_name' => 'web',
                'parent' => 'Sponsor Category'
            ],

            //Faq
            [
                'name' => 'View Faq',
                'guard_name' => 'web',
                'parent' => 'Faq'
            ],
            [
                'name' => 'Add Faq',
                'guard_name' => 'web',
                'parent' => 'Faq'
            ],
            [
                'name' => 'Edit Faq',
                'guard_name' => 'web',
                'parent' => 'Faq'
            ],
            [
                'name' => 'Delete Faq',
                'guard_name' => 'web',
                'parent' => 'Faq'
            ],

            //Faq Category
            [
                'name' => 'View Faq Category',
                'guard_name' => 'web',
                'parent' => 'Faq Category'
            ],
            [
                'name' => 'Add Faq Category',
                'guard_name' => 'web',
                'parent' => 'Faq Category'
            ],
            [
                'name' => 'Edit Faq Category',
                'guard_name' => 'web',
                'parent' => 'Faq Category'
            ],
            [
                'name' => 'Delete Faq Category',
                'guard_name' => 'web',
                'parent' => 'Faq Category'
            ],

            //User
            [
                'name' => 'View User',
                'guard_name' => 'web',
                'parent' => 'User'
            ],
            [
                'name' => 'Edit User',
                'guard_name' => 'web',
                'parent' => 'User'
            ],
            [
                'name' => 'Delete User',
                'guard_name' => 'web',
                'parent' => 'User'
            ],
            [
                'name' => 'Assign Expert',
                'guard_name' => 'web',
                'parent' => 'User'
            ],
            [
                'name' => 'Invite For Conference',
                'guard_name' => 'web',
                'parent' => 'User'
            ],
            [
                'name' => 'Pass Designation',
                'guard_name' => 'web',
                'parent' => 'User'
            ],
            [
                'name' => 'Merge User',
                'guard_name' => 'web',
                'parent' => 'User'
            ],
            [
                'name' => 'Reset Password',
                'guard_name' => 'web',
                'parent' => 'User'
            ],
            //User Management And Role
            [
                'name' => 'View Role',
                'guard_name' => 'web',
                'parent' => 'Role'
            ],
            [
                'name' => 'Add Role',
                'guard_name' => 'web',
                'parent' => 'Role'
            ],
            [
                'name' => 'Edit Role',
                'guard_name' => 'web',
                'parent' => 'Role'
            ],
            [
                'name' => 'Assign Role',
                'guard_name' => 'web',
                'parent' => 'Role'
            ],

            //Download
            [
                'name' => 'View Download',
                'guard_name' => 'web',
                'parent' => 'Download'
            ],
            [
                'name' => 'Add Download',
                'guard_name' => 'web',
                'parent' => 'Download'
            ],
            [
                'name' => 'Edit Download',
                'guard_name' => 'web',
                'parent' => 'Download'
            ],
            [
                'name' => 'Delete Download',
                'guard_name' => 'web',
                'parent' => 'Download'
            ],
            [
                'name' => 'Change Featured Download',
                'guard_name' => 'web',
                'parent' => 'Download',
            ],

            //News/Notice
            [
                'name' => 'View News/Notice',
                'guard_name' => 'web',
                'parent' => 'News/Notice'
            ],
            [
                'name' => 'Add News/Notice',
                'guard_name' => 'web',
                'parent' => 'News/Notice'
            ],
            [
                'name' => 'Edit News/Notice',
                'guard_name' => 'web',
                'parent' => 'News/Notice'
            ],
            [
                'name' => 'Delete News/Notice',
                'guard_name' => 'web',
                'parent' => 'News/Notice'
            ],
            [
                'name' => 'Change Featured News/Notice',
                'guard_name' => 'web',
                'parent' => 'News/Notice',
            ],

            //Hotel
            [
                'name' => 'View Hotel',
                'guard_name' => 'web',
                'parent' => 'Hotel'
            ],
            [
                'name' => 'Add Hotel',
                'guard_name' => 'web',
                'parent' => 'Hotel'
            ],
            [
                'name' => 'Edit Hotel',
                'guard_name' => 'web',
                'parent' => 'Hotel'
            ],
            [
                'name' => 'Delete Hotel',
                'guard_name' => 'web',
                'parent' => 'Hotel'
            ],
            [
                'name' => 'Change Featured Hotel',
                'guard_name' => 'web',
                'parent' => 'Hotel'
            ],
        ];
        Permission::insert($permission);
    }
}
