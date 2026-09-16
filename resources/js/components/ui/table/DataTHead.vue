<script setup lang="ts">
const props = defineProps({
    class: String
});
</script>

<template>
    <thead :class="props.class">
        <slot></slot>
    </thead>
</template>
