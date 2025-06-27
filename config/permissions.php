<?php

return [

    'route_permissions' => [
        // Conference registrant Permission
        'conference.conference-registration.index' => 'View Conference Registration',
        'conference.conference-registration.show' => 'View Conference Registration',
        'conference.conference-registration.registerForExceptionalCase' => 'Exceptional Case',
        'conference.conference-registration.registerForExceptionalCaseSubmit' => 'Exceptional Case',
        'conference.conference-registration.registrationOrInvitation' => 'Registration And Invitation',
        'conference.conference-registration.registrationOrInvitationSubmit' => 'Registration And Invitation',
        'conference.conference-registration.addPerson' => 'Add People',
        'conference.conference-registration.addPersonSubmit' => 'Add People',
        'conference.conference-registration.convertRegistrantType' => 'Convert Registrant Type',
        'conference.conference-registration.convertRegistrantTypesubmit' => 'Convert Registrant Type',
        'conference.conference-registration.excelExport' => 'Export',

        //conference pass setting
        'pass-setting.index' => 'View Pass Setting',
        'pass-setting.create' => 'Add Pass Setting',
        'pass-setting.store' => 'Add Pass Setting',
        'pass-setting.edit' => 'Edit Pass Setting',
        'pass-setting.update' => 'Edit Pass Setting',

        //conference pass setting
        'conference-certificate.index' => 'View Certificate Setting',
        'conference-certificate.create' => 'Add Certificate Setting',
        'conference-certificate.store' => 'Add Certificate Setting',
        'conference-certificate.edit' => 'Edit Certificate Setting',
        'conference-certificate.update' => 'Edit Certificate Setting',

        //submission permission
        'submission.index' => 'View Submission',
        'submission.show' => 'View Submission',
        'submission.expertForwardForm' => 'Expert Assign',
        'submission.expertForward' => 'Expert Assign',
        'submission.sentToAuthorForm' => 'Change Request Status',
        'submission.sentToAuthor' => 'Change Request Status',
        'submission.viewDiscussion' => 'View Discussion',
        'submission.convertPresentationTypeRequest' => 'Convert Presentation Type',
        'submission.viewScore' => 'View Score',
        'submission.sendMail' => 'Send Mail',
        'submission.sendMailSubmit' => 'Send Mail',
        'submission.get.users' => 'Send Mail',
        'submission.author.index' => 'View Author',

        //submission setting permission
        'submission.setting' => 'View Submission Setting',
        'submission.settingSubmit' => 'Add/Edit Submission Setting',

        //submission major track permission
        'submission.category-majortrack.index' => 'View Category/Major Track',
        'submission.category-majortrack.create' => 'Add Category/Major Track',
        'submission.category-majortrack.store' => 'Add Category/Major Track',
        'submission.category-majortrack.edit' => 'Edit Category/Major Track',
        'submission.category-majortrack.update' => 'Edit Category/Major Track',
        'submission.category-majortrack.destroy' => 'Delete Category/Major Track',

        // Scientific Session Category Permissions
        'category.index' => 'View Scientific Session Category',
        'category.create' => 'Add Scientific Session Category',
        'category.store' => 'Add Scientific Session Category',
        'category.edit' => 'Edit Scientific Session Category',
        'category.update' => 'Edit Scientific Session Category',
        'category.destroy' => 'Delete Scientific Session Category',

        //Scientific Session Hall Permissions
        'hall.index' => 'View Scientific Session Hall',
        'hall.create' => 'Add Scientific Session Hall',
        'hall.store' => 'Add Scientific Session Hall',
        'hall.edit' => 'Edit Scientific Session Hall',
        'hall.update' => 'Edit Scientific Session Hall',
        'hall.destroy' => 'Delete Scientific Session Hall',

        //Scientific Session Permissions
        'scientific-session.index' => 'View Scientific Session',
        'scientific-session.create' => 'Add Scientific Session',
        'scientific-session.store' => 'Add Scientific Session',
        'scientific-session.edit' => 'Edit Scientific Session',
        'scientific-session.update' => 'Edit Scientific Session',
        'scientific-session.destroy' => 'Delete Scientific Session',

        //Poll Permissions
        'poll.index' => 'View Poll',
        'poll.create' => 'Create Poll',
        'poll.store' => 'Create Poll',
        'poll.edit' => 'Edit Poll',
        'poll.update' => 'Edit Poll',
        'poll.destroy' => 'Delete Poll',

        // Workshop permissions
        'workshop.index' => 'View Workshop',
        'workshop.create' => 'Add Workshop',
        'workshop.store' => 'Add Workshop',
        'workshop.edit' => 'Edit Workshop',
        'workshop.update' => 'Edit Workshop',
        'workshop.destroy' => 'Delete Workshop',
        'workshop.allocatePriceForm' => 'Add/Update Registration Price',
        'workshop.allocatePriceSubmit' => 'Add/Update Registration Price',
        'workshop.workshop-registration.registerForExceptionalCase' => 'Regster User in Exceptional Case',
        'workshop.workshop-registration.registerForExceptionalCaseSubmit' => 'Regster User in Exceptional Case',
        'workshop.workshop-registration.registerForNewUser' => 'Regster New User',
        'workshop.workshop-registration.registerForNewUserSubmit' => 'Regster New User',

        //Workshop Registrant Permission
        'workshop.workshop-registration.index' => 'View Workshop Registrant',

        //Workshop Pass Setting Permission
        'workshop-pass-settings.index' => 'View Workshop Pass Setting',
        'workshop-pass-settings.create' => 'Add Workshop Pass Setting',
        'workshop-pass-settings.store' => 'Add Workshop Pass Setting',
        'workshop-pass-settings.edit' => 'Edit Workshop Pass Setting',
        'workshop-pass-settings.update' => 'Edit Workshop Pass Setting',

        //  Trainer Permission
        'workshop.workshop-trainer.index' => 'View Workshop Trainer',
        'workshop.workshop-trainer.create' => 'Add Workshop Trainer',
        'workshop.workshop-trainer.store' => 'Add Workshop Trainer',
        'workshop.workshop-trainer.edit' => 'Edit Workshop Trainer',
        'workshop.workshop-trainer.update' => 'Edit Workshop Trainer',
        'workshop.workshop-trainer.destroy' => 'Delete Workshop Trainer',

        //CommitteeDesignation Permission
        'committe-designation.index' => 'View Committee Designation',
        'committe-designation.create' => 'Add Committee Designation',
        'committe-designation.store' => 'Add Committee Designation',
        'committe-designation.edit' => 'Edit Committee Designation',
        'committe-designation.update' => 'Edit Committee Designation',
        'committe-designation.destroy' => 'Delete Committee Designation',

        //Committee Permission
        'committe.index' => 'View Committee',
        'committe.create' => 'Add Committee',
        'committe.store' => 'Add Committee',
        'committe.edit' => 'Edit Committee',
        'committe.update' => 'Edit Committee',
        'committe.destroy' => 'Delete Committee',

        //Committee member Permission
        'committeeMember.index' => 'View Committee Member',
        'committeeMember.create' => 'Add Committee Member',
        'committeeMember.store' => 'Add Committee Member',
        'committeeMember.edit' => 'Edit Committee Member',
        'committeeMember.update' => 'Edit Committee Member',
        'committeeMember.destroy' => 'Delete Committee Member',
        'committeeMember.changeFeatured' => 'Change Featured Committee Member',

        //Sponsor Category Permission
        'sponsor-category.index' => 'View Sponsor Category',
        'sponsor-category.create' => 'Add Sponsor Category',
        'sponsor-category.store' => 'Add Sponsor Category',
        'sponsor-category.edit' => 'Edit Sponsor Category',
        'sponsor-category.update' => 'Edit Sponsor Category',
        'sponsor-category.destroy' => 'Delete Sponsor Category',

        //Sponsor Permission
        'sponsor.index' => 'View Sponsor',
        'sponsor.create' => 'Add Sponsor',
        'sponsor.store' => 'Add Sponsor',
        'sponsor.edit' => 'Edit Sponsor',
        'sponsor.update' => 'Edit Sponsor',
        'sponsor.destroy' => 'Delete Sponsor',
        'sponsor.changeStatus' => 'Change Publish Sponsor',
        'sponsor.addParticipant' => 'Add Participant Sponsor',
        'sponsor.addParticipantSubmit' => 'Add Participant Sponsor',

        //Faq Category Permission
        'faq-category.index' => 'View Faq Category',
        'faq-category.create' => 'Add Faq Category',
        'faq-category.store' => 'Add Faq Category',
        'faq-category.edit' => 'Edit Faq Category',
        'faq-category.update' => 'Edit Faq Category',
        'faq-category.destroy' => 'Delete Faq Category',

        //Faq Permission
        'faq.index' => 'View Faq',
        'faq.create' => 'Add Faq',
        'faq.store' => 'Add Faq',
        'faq.edit' => 'Edit Faq',
        'faq.update' => 'Edit Faq',
        'faq.destroy' => 'Delete Faq',

        //View User
        'signup-user.index' => 'View User',
        'signup-user.show' => 'View User',
        'signup-user.editProfile' => 'Edit User',
        'signup-user.editProfileSubmit' => 'Edit User',
        'signup-user.destroy' => 'Delete User',
        'signup-user.inviteForConference' => 'Invite For Conference',
        'signup-user.inviteForConferenceSubmit' => 'Invite For Conference',
        'signup-user.makeExpert' => 'Assign Expert',

        //Role Permission
        'roles.index' => 'View Role',
        'roles.create' => 'Add Role',
        'roles.store' => 'Add Role',
        'roles.edit' => 'Edit Role',
        'roles.update' => 'Edit Role',
        'assignRoleForm' => 'Assign Role',
        'assignRoleFormSubmit' => 'Assign Role',

        //Download Permission
        'download.index' => 'View Download',
        'download.create' => 'Add Download',
        'download.store' => 'Add Download',
        'download.edit' => 'Edit Download',
        'download.update' => 'Edit Download',
        'download.destroy' => 'Delete Download',
        'download.changeStatus' => 'Change Featured Download',

        //News/Notice Permission
        'notice.index' => 'View News/Notice',
        'notice.show' => 'View News/Notice',
        'notice.create' => 'Add News/Notice',
        'notice.store' => 'Add News/Notice',
        'notice.edit' => 'Edit News/Notice',
        'notice.update' => 'Edit News/Notice',
        'notice.destroy' => 'Delete News/Notice',
        'notice.changeFeatured' => 'Change Featured News/Notice',

        //Hotel Permission
        'hotel.index' => 'View Hotel',
        'hotel.create' => 'Add Hotel',
        'hotel.store' => 'Add Hotel',
        'hotel.edit' => 'Edit Hotel',
        'hotel.update' => 'Edit Hotel',
        'hotel.destroy' => 'Delete Hotel',
        'hotel.changeStatus' => 'Change Featured Hotel',
    ]

];
