<template>
    <AuthenticatedLayout>

    </AuthenticatedLayout>
</template>
<script setup lang="ts">
interface Spgc {
    records: {
        spgc: any
    }
}
interface Docs {
    records: {
        docs: any
    }
}
interface Approved {
    records: {
        approved: any
    }
}
interface Released {
    records: {
        released: any
    }
}
defineProps<{
    spgc: Spgc,
    docs: Docs,
    approved: Approved,
    released: Released,
}>();
</script>
