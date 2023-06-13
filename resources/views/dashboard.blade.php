<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Your Personal access tokens') }}
        </h2>
    </x-slot>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden sm:rounded-lg">
                <p class="mb-6">
                    <x-nav-link href="{{ route('token.showForm') }}">
                        Create new token
                    </x-nav-link>
                </p>
                @if (count($tokens) > 0)
                    <div class="flex flex-col">
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                <div class="shadow-md overflow-hidden sm:rounded-lg">
                                    <table class="w-full text-md text-left text-gray-500">
                                        <thead
                                            class="uppercase text-sm bg-gray-400">
                                        <tr>
                                            <th scope="col" class="px-6 py-3">
                                                Name
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Last used
                                            </th>
                                            <th scope="col" class="relative px-6 py-3">
                                                <span class="sr-only">Delete</span>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($tokens as $token)
                                            <tr class="bg-white border-b hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{$token->name}}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{$token->last_used_at ? $token->last_used_at->diffForHumans() : ''}}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <form method="POST"
                                                          action="{{ route('token.delete', ['token' => $token->id]) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-danger-button>Delete</x-danger-button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-center">You don't have personal access tokens yet</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
