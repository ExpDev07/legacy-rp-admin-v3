<template>
    <div>
        <v-section class="overflow-x-auto" :noFooter="true" :noHeader="true">
            <div class="w-full flex flex-wrap max-w-screen-md m-auto text-justify">
                <h1 class="dark:text-white mb-5">
                    Server-Logs: Disconnect reasons
                </h1>

                <p>
                    Each one of these disconnect reasons was tested on the test server. This should help in determining if what you are seeing
                    in the server-logs lines up with what another player is telling you really happened. Disconnect reasons in the server-logs
                    wont look exactly the same as the ones in the screenshots below, but should look similar.
                </p>

                <div class="w-full h-px bg-gray-700 dark:bg-gray-300 my-4"></div>

                <img src="/images/docs/reason_exiting.png" class="w-full shadow-md mb-3 border border-gray-500" />

                <p class="mb-1">
                    This disconnect reason is triggered...
                </p>

                <ul class="w-full pl-8 list-disc">
                    <li>...by pressing <i>F8</i> and typing <i>quit</i>.</li>
                    <li>...by pressing <i>ALT</i> and <i>F4</i>.</li>
                    <li>...by pressing <i>Escape</i> and navigating to the <i>Game</i> section of the settings and selecting <i>Exit Game</i>.</li>
                </ul>

                <div class="w-full h-px bg-gray-700 dark:bg-gray-300 my-4"></div>

                <img src="/images/docs/reason_disconnected.png" class="w-full shadow-md mb-3 border border-gray-500" />

                <p class="mb-1">
                    This disconnect reason is triggered...
                </p>

                <ul class="w-full pl-8 list-disc">
                    <li>...by pressing <i>F8</i> and typing <i>disconnect</i>.</li>
                    <li>...by pressing <i>Escape</i> and navigating to the <i>Game</i> section of the settings and selecting <i>Disconnect</i>.</li>
                </ul>

                <div class="w-full h-px bg-gray-700 dark:bg-gray-300 my-4"></div>

                <img src="/images/docs/reason_timeout.png" class="w-full shadow-md mb-3 border border-gray-500" />

                <p class="mb-1">
                    This disconnect reason is triggered...
                </p>

                <ul class="w-full pl-8 list-disc">
                    <li>...by opening <i>Task Manager</i> and <i>ending the FiveM process</i>.</li>
                    <li>...by <i>holding the power button</i> of your computer.</li>
                    <li>...by <i>gracefully shutting down</i> your computer (Windows Key -> Shutdown).</li>
                    <li>...by <i>unplugging your ethernet cable</i> or randomly <i>loosing internet connection</i>.</li>
                </ul>

                <div class="w-full h-px bg-gray-700 dark:bg-gray-300 my-4"></div>

                <img src="/images/docs/reason_crash.png" class="w-full shadow-md mb-3 border border-gray-500" />

                <p class="mb-1">
                    This disconnect reason is triggered...
                </p>

                <ul class="w-full pl-8 list-disc">
                    <li>...by the <i>game crashing</i> (this can happen through a number of reasons).</li>
                </ul>
            </div>
        </v-section>
    </div>
</template>

<script>
import Layout from './../../Layouts/App';
import VSection from './../../Components/Section';
import Badge from './../../Components/Badge';
import Pagination from './../../Components/Pagination';

export default {
    layout: Layout,
    components: {
        VSection,
        Badge,
        Pagination,
    },
}
</script>
