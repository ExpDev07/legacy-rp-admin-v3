<template>
<div class="flex">
    <!-- Branding / Logo -->
    <div class="flex-shrink-0 bg-indigo-800 text-white text-center w-56 px-12 py-4">
        <h1 class="text-lg">
            <inertia-link href="/">
                Legacy<span class="font-bold">RP</span>
            </inertia-link>
        </h1>
    </div>
    <!-- Nav -->
    <div class="flex justify-between items-center bg-gray-300 w-full shadow px-12 py-4">
        <!-- Left side -->
        <p class="italic">
            {{ $page.inspiration }}
        </p>

        <!-- Right side -->
        <div class="flex items-center">
            <inertia-link class="hover:text-gray-800 mr-3" v-bind:href="'/players/' + $page.auth.player.steamIdentifier">
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
