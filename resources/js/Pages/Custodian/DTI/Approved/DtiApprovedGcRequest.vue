<template>
    <AuthenticatedLayout>
        <a-table :data-source="records" :columns="columns"/>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
interface Records{

}
// defineProps({
//     records: Records
// });
</script>
