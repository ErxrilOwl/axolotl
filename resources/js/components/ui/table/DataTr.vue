<script setup lang="ts">
const props = defineProps({
    class: String
});
</script>

<template>
    <tr class="border-t border-gray-100 dark:border-gray-800" :class="props.class">
        <slot></slot>
    </tr>
</template>
