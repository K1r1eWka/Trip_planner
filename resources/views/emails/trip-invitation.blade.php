<x-mail::message>
# You're invited to join {{ $trip->name }}!

Someone has invited you to join the trip **{{ $trip->name }}** on Trip Planner.

Use the invite code below to join:

<x-mail::panel>
# {{ $trip->invite_code }}
</x-mail::panel>

<x-mail::button :url="config('app.url') . '/join'">
Join the Trip
</x-mail::button>

See you on the trip!<br>
**Trip Planner**
</x-mail::message>