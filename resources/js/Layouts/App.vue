<template>
<div class="flex flex-col h-screen">
    <!-- Navbar -->
    <navbar />

    <div class="flex flex-grow overflow-hidden">
        <!-- Sidebar -->
        <sidebar class="flex-shrink-0" />

        <div class="flex flex-col flex-grow overflow-y-auto" scroll-region>
            <!-- Content -->
            <div class="flex-grow p-12">
                <!-- Flash message -->
                <div>
                    <flash-message />
                </div>
                <slot />
            </div>
            <!-- Footer -->
            <foot />
        </div>
    </div>
</div>
</template>

<script>
import FlashMessage from './../Components/FlashMessage';
import Navbar from './../Components/Navbar';
import Sidebar from './../Components/Sidebar';
import Foot from './../Components/Footer';

export default {
    components: {
        FlashMessage,
        Foot,
        Sidebar,
        Navbar,
    },
};
</script>
