<template>
    <div class="flex">

        <!-- Branding / Logo -->
        <div class="flex-shrink-0 w-56 px-12 py-4 text-center text-white bg-gray-900">
            <h1 class="text-lg">
                <inertia-link href="/">
                    Legacy<span class="font-bold">RP</span>
                </inertia-link>
            </h1>
        </div>

        <!-- Nav -->
        <nav class="flex items-center justify-between w-full px-12 py-4 text-white bg-gray-900 shadow">
            <!-- Left side -->
            <p class="italic"></p>

            <!-- Right side -->
            <div class="flex items-center space-x-6">
                <inertia-link class="hover:text-gray-100" v-bind:href="'/players/' + $page.auth.player.steamIdentifier">
                    {{ $page.auth.user.name }}
                </inertia-link>
                <inertia-link class="px-4 py-1 text-white bg-red-500 rounded hover:bg-red-600" method="POST" href="/logout">
                    Logout
                </inertia-link>
            </div>
        </nav>

    </div>
</template>

<script>
import Icon from './Icon';

export default {
    components: {
        Icon,
    }
}
</script>
