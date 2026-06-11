<x-mail::message>
# {{ $newMember->name }} joined your trip!

**{{ $newMember->name }}** has just joined your trip **{{ $trip->name }}** on Trip Planner.

<x-mail::button :url="config('app.url') . '/trips/' . $trip->id">
View Trip
</x-mail::button>

**Trip Planner**
</x-mail::message>