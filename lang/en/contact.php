<?php

declare(strict_types=1);

return [

    'title' => 'Send a message',

    'fields' => [
        'name' => 'Your name',
        'email' => 'Email',
        'inquiry_type' => 'What is it about?',
        'budget' => 'Estimated budget',
        'subject' => 'Subject',
        'message' => 'Message',
        'website' => 'Website',
    ],

    'attributes' => [
        'name' => 'name',
        'email' => 'email',
        'inquiry_type' => 'inquiry type',
        'budget' => 'budget',
        'subject' => 'subject',
        'message' => 'message',
    ],

    'placeholders' => [
        'name' => 'Jane Doe',
        'email' => 'jane@company.com',
        'budget' => 'e.g. $2,000 – $5,000',
        'subject' => 'What would you like to discuss?',
        'message' => 'Tell me about the role or the project…',
    ],

    'hints' => [
        'budget' => 'A rough range is fine.',
    ],

    'submit' => 'Send message',
    'sending' => 'Sending…',
    'privacy' => 'Your details are only used to reply to you.',

    'success' => [
        'title' => 'Thanks — your message is on its way!',
        'body' => 'I’ll get back to you within 48 hours.',
        'again' => 'Send another message',
    ],

    'errors' => [
        'too_fast' => 'That was quick! Please check your message and send it again.',
        'rate_limited' => 'You’ve sent several messages already. Please try again in :minutes minute(s).',
    ],

];
