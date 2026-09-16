<script setup lang="ts">

type Numberish = number | string;

interface Props {
    class?: String,
    colspan?: Numberish | undefined
}

const props = defineProps<Props>();
</script>

<template>
    <td class="px-5 py-4 sm:px-6" :class="props.class" :colspan="colspan">
        <slot />
    </td>
</template>
