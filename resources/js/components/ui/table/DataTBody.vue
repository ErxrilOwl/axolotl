<script setup lang="ts">
const props = defineProps({
    class: String
});
</script>

<template>
    <tbody class="divide-y divide-gray-200 dark:divide-gray-700" :class="props.class">
        <slot></slot>
    </tbody>
</template>
