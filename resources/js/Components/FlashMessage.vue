<template>
    <div v-if="show">
        <!-- Success -->
        <alert v-if="$page.flash.success" class="bg-green-500 mb-14">
            <p>
                {{ $page.flash.success }}
            </p>
        </alert>
        <!-- Error -->
        <alert v-if="$page.flash.error" class="bg-red-500 mb-14">
            <p>
                {{ $page.flash.error }}
            </p>
        </alert>
    </div>
</template>

<script>
import Alert from './Alert';

export default {
    components: {
        Alert,
    },
    data() {
        return {
            show: true,
        };
    },
}
</script>
