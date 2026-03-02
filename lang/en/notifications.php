<?php

return [
    'salutation' => 'Regards,<br>The Famous Mexican Restaurants Team',

    // Review Approved Notification
    'review_approved' => [
        'subject' => 'Your Review Has Been Approved!',
        'greeting' => 'Hello :name!',
        'message' => 'Great news! Your review for **:restaurant** has been approved and is now live on our site.',
        'details' => 'Your rating: :rating/5 stars - ":title"',
        'action' => 'View Your Review',
        'thanks' => 'Thank you for contributing to our community and helping others discover authentic Mexican restaurants!',
        'notification_message' => 'Your review for :restaurant has been approved',
    ],

    // Suggestion Approved Notification
    'suggestion_approved' => [
        'subject' => 'Your Restaurant Suggestion Has Been Approved!',
        'greeting' => 'Hello :name!',
        'message' => 'Excellent news! Your suggestion for **:restaurant** has been reviewed and approved.',
        'details' => 'Location: :city, :state',
        'action' => 'View Restaurant Page',
        'thanks' => 'Thank you for helping us expand our directory of authentic Mexican restaurants. Your contribution makes a difference!',
        'notification_message' => 'Your suggestion for :restaurant has been approved',
    ],
];
