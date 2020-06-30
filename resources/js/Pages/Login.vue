<template>
<div class="flex flex-col items-center justify-center h-screen p-5" v-bind:style="{ background: 'url(https://fivem.net/17c4e5d0b35262c228dcf66c98365982.jpg)'  }">
    <div class="bg-gray-300 p-16 max-w-3xl">
        <!-- Flash message -->
        <div>
            <flash-message />
        </div>
        <!-- Title -->
        <div class="text-center mb-8">
            <span class="fab fa-steam text-5xl text-gray-800 mb-4"></span>
            <h1 class="text-3xl font-semibold mb-5">
                Login with your Steam account.
            </h1>
            <p>
                Please login with the same account that you have your staff permissions on.
            </p>
        </div>

        <a class="block bg-indigo-600 hover:bg-orange-500 text-white text-center p-5" href="/auth/login/steam">
            Login with Steam
        </a>
    </div>
</div>
</template>

<script>
import FlashMessage from './../../Components/FlashMessage';

export default {
    components: {
        FlashMessage,
    },
};
</script>
