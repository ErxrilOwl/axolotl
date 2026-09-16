<script setup lang="ts">
const props = defineProps({
    class: String
});
</script>

<template>
    <th class="px-5 py-3 text-left sm:px-6" :class="props.class">
        <slot />
    </th>
</template>
