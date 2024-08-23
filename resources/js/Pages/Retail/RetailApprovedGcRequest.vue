<template>
    <AuthenticatedLayout>
        <a-table size="small" :data-source="record.data" bordered :columns="columns" :pagination=false>

            <template #bodyCell="{ column, record }">
                <template v-if="column.key == 'status'">
                    <a-button @click="openModal(record.agcr_request_relnum)">
                        <template #icon>
                            <HighlightOutlined />
                        </template>
                    </a-button>
                </template>
            </template>
        </a-table>
        <pagination class="mt-6" :datarecords="record" />

        <create-entry-gc v-model:open="open" :record="data" />

    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const open = ref(false);

const form = useForm({
    agc_num: null,
});

defineProps({
    record: Object,
    data: Object,
    columns: String,
});


const openModal = (agc_num) => {

    form.agc_num = agc_num;

    form.get(route('retail.approved.request'), {
        onSuccess: () => {
            open.value = true;
        },
        preserveState: true,
    })
}

onMounted(() => {
    handlePageRefresh();
});

const handlePageRefresh = () => {
    router.post(route('retail.manage.remove'));
}





</script>
