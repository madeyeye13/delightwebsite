<x-mail::message>
# New Testimonial Submitted

A customer has submitted a testimonial that is awaiting your approval.

<x-mail::panel>
**Name:** {{ $testimonial->name }}
**Location:** {{ $testimonial->location ?? 'Not provided' }}
@if($testimonial->rating)
**Rating:** {{ $testimonial->rating }}/5 ★
@endif
**Message:** {{ $testimonial->quote }}
</x-mail::panel>

Please review and approve or reject this testimonial in the admin panel.

<x-mail::button :url="$adminUrl">
Review in Admin
</x-mail::button>

**1st Delightsome Admin**
</x-mail::message>
