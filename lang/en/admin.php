<?php

declare(strict_types=1);

return [

    'brand' => 'Portfolio Admin',

    'navigation' => [
        'inbox' => 'Inbox',
        'content' => 'Content',
        'settings' => 'Settings',
    ],

    'resources' => [
        'experience' => ['singular' => 'experience', 'plural' => 'Experience'],
        'skill_group' => ['singular' => 'skill group', 'plural' => 'Skills'],
        'service' => ['singular' => 'service', 'plural' => 'Services'],
        'project' => ['singular' => 'project', 'plural' => 'Projects'],
        'education' => ['singular' => 'education', 'plural' => 'Education'],
        'certification' => ['singular' => 'certification', 'plural' => 'Certifications'],
        'contact_message' => [
            'singular' => 'message',
            'plural' => 'Messages',
            'badge_tooltip' => 'New messages',
        ],
    ],

    'pages' => [
        'site_settings' => ['title' => 'Site settings'],
    ],

    'tabs' => [
        'overview' => 'Overview',
        'case_study' => 'Case study',
        'media' => 'Media',
        'links' => 'Links',
        'seo' => 'SEO',
        'profile' => 'Profile',
        'about' => 'About',
        'contact' => 'Contact & social',
    ],

    'sections' => [
        'position' => 'Position',
        'details' => 'Details',
        'sender' => 'Sender',
        'message' => 'Message',
        'metadata' => 'Metadata',
    ],

    'fields' => [
        'about_body' => 'About text',
        'about_highlights' => 'Highlights',
        'alt' => 'Alt text',
        'availability_status' => 'Availability',
        'avatar' => 'Profile photo',
        'bullet_points' => 'Bullet points',
        'completed_on' => 'Completed on',
        'cover_image' => 'Cover image',
        'cover_image_alt' => 'Cover alt text',
        'created_at' => 'Received',
        'credential_urls' => 'Credential links',
        'cv_file' => 'CV (PDF)',
        'end_date' => 'End date',
        'end_year' => 'End year',
        'freelancer_url' => 'Freelancer URL',
        'full_name' => 'Full name',
        'github_url' => 'GitHub URL',
        'images' => 'Gallery',
        'inquiry_type' => 'Inquiry type',
        'ip_hash' => 'IP hash',
        'is_featured' => 'Featured',
        'is_in_progress' => 'In progress',
        'is_published' => 'Published',
        'is_visible' => 'Visible',
        'linkedin_url' => 'LinkedIn URL',
        'meta_description' => 'Meta description',
        'meta_title' => 'Meta title',
        'my_role' => 'My role',
        'og_image' => 'Social share image (Open Graph)',
        'platforms_integrated' => 'Platforms integrated',
        'projects_shipped' => 'Projects shipped',
        'short_bio' => 'Value proposition',
        'short_description' => 'Short description',
        'skills_count' => 'Skills',
        'source_code_url' => 'Source code URL',
        'start_date' => 'Start date',
        'start_year' => 'Start year',
        'started_on' => 'Started on',
        'tech_stack' => 'Tech stack',
        'url' => 'URL',
        'user_agent' => 'User agent',
        'views_count' => 'Views',
        'whatsapp' => 'WhatsApp',
        'years_of_experience' => 'Years of experience',
    ],

    'help' => [
        'alt_text' => 'Describe the image for screen readers and search engines.',
        'contact_email' => 'Shown on the site and used as the recipient of contact-form notifications.',
        'cover_image' => 'JPG, PNG or WebP up to 4 MB. A 16:9 image works best.',
        'cv_file' => 'Stored privately. Visitors can only download it through the tracked download link.',
        'end_date_present' => 'Leave empty for your current position ("Present").',
        'hero_stat' => 'Shown in the hero. Leave empty to hide it.',
        'is_published' => 'Unpublished projects are hidden from the site and return 404.',
        'meta_description' => 'Up to 160 characters. Falls back to the summary.',
        'meta_title' => 'Up to 70 characters. Falls back to the title.',
        'og_image' => 'Shown when the site is shared on social media. 1200×630 px recommended.',
        'short_bio' => 'One-line value proposition shown under the headline.',
        'slug' => 'Used in the project URL: /projects/{slug}',
        'source_code_url' => 'Linked in the footer: "This site is open source".',
        'whatsapp' => 'International format, e.g. +20 1xx xxx xxxx.',
    ],

    'actions' => [
        'add_bullet_point' => 'Add bullet point',
        'add_credential_link' => 'Add credential link',
        'add_gallery_image' => 'Add image',
        'add_highlight' => 'Add highlight',
        'add_link' => 'Add link',
        'reply_by_email' => 'Reply by email',
        'save' => 'Save changes',
        'mark_as' => [
            'new' => 'Mark as new',
            'read' => 'Mark as read',
            'replied' => 'Mark as replied',
            'archived' => 'Archive',
        ],
    ],

    'notifications' => [
        'settings_saved' => 'Settings saved',
        'status_updated' => 'Status updated',
    ],

    'widgets' => [
        'new_messages' => 'New messages',
        'messages_this_month' => 'Messages this month',
        'cv_downloads' => 'CV downloads',
        'last_30_days' => 'Last 30 days',
        'top_projects' => 'Most viewed projects',
    ],

];
