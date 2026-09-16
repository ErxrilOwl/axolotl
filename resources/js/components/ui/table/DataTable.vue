<script setup lang="ts">
import TableWrapper from './DataTableWrapper.vue';

const props = defineProps({
    class: String
});

</script>
<template>
    <TableWrapper :class="props.class ?? ''">
        <slot />
    </TableWrapper>
</template>
