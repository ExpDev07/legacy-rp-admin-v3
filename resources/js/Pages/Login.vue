<template>
    <div
        class="flex flex-col items-center justify-center h-screen p-5"
        :style="{ background: 'url(https://fivem.net/17c4e5d0b35262c228dcf66c98365982.jpg)'  }"
    >
        <div class="p-16 max-w-3xl bg-white shadow">

            <!-- Flash message -->
            <div>
                <flash-message />
            </div>

            <!-- Content -->
            <div class="text-center mb-8">
                <span class="fab fa-steam text-6xl text-gray-800 mb-6"></span>
                <h1 class="text-3xl font-semibold mb-6">
                    Login with your Steam account.
                </h1>
                <p class="text-gray-700">
                    Please login with the same Steam account that you have your staff permissions on. Using a different
                    one will result in you not being able to access the server panel.
                </p>
            </div>

            <a class="block bg-indigo-600 hover:bg-orange-500 text-white text-center p-5" href="/auth/login/steam">
                Login with Steam
            </a>

        </div>
    </div>
</template>

<script>
import FlashMessage from './../Components/FlashMessage';

export default {
    components: {
        FlashMessage,
    },
};
</script>
