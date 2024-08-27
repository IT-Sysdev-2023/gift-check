<template>
    <AuthenticatedLayout>
        <a-table size="small" :data-source="record.data" bordered :columns="columns" :pagination=false>

            <template #bodyCell="{ column, record }">
                <template v-if="column.key == 'status'">
                    <div v-if="record.agcr_rec == 0">
                        <a-button @click="openModal(record.agcr_request_relnum)" type="primary" ghost>
                            <template #icon>
                                <HighlightOutlined />
                            </template>
                            Setup Entry
                        </a-button>
                    </div>
                    <div v-else>
                        <a-tag color="default">
                            <template #icon>
                                <minus-circle-outlined />
                            </template>
                            Closed Entry
                        </a-tag>
                    </div>
                </template>
            </template>
        </a-table>
        <pagination class="mt-6" :datarecords="record" />

        <create-entry-gc v-model:open="open" :record="data" @close-modal="close" />

    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, createVNode } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { onBeforeMount } from 'vue';


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
const close = () => {
    open.value = false;
}

onBeforeMount(() => {
    router.post(route('retail.manage.remove'))
});





</script>
