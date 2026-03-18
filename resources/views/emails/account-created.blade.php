<x-mail::message>
# Welcome, {{ $userName }}!

Your account at **1st Delightsome** has been created.

**Email:** {{ $userEmail }}

@if ($temporaryPassword)
A temporary password has been set for your account:

<x-mail::panel>
**{{ $temporaryPassword }}**
</x-mail::panel>

Please log in and change it to something memorable.
@endif

<x-mail::button :url="$loginUrl">
Login to Your Account
</x-mail::button>

Thanks for shopping with us,<br>
**1st Delightsome**
</x-mail::message>
