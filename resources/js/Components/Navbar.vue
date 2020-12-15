<template>
    <div class="flex">
        <!-- Branding / Logo -->
        <div class="flex-shrink-0 bg-gray-900 text-white text-center w-56 px-12 py-4">
            <h1 class="text-lg">
                <inertia-link href="/">
                    Legacy<span class="font-bold">RP</span>
                </inertia-link>
            </h1>
        </div>
        <!-- Nav -->
        <div class="flex justify-between items-center bg-gray-900 text-white w-full shadow px-12 py-4">
            <!-- Left side -->
            <p class="italic"></p>

            <!-- Right side -->
            <div class="flex items-center space-x-6">
                <inertia-link class="hover:text-gray-100" v-bind:href="'/players/' + $page.auth.player.steamIdentifier">
                    {{ $page.auth.user.name }}
                </inertia-link>
                <inertia-link class="bg-red-500 hover:bg-red-600 text-white rounded py-1 px-4" method="POST" href="/logout">
                    Logout
                </inertia-link>
            </div>
        </div>
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
