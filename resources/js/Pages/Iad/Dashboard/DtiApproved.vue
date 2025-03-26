<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const props = defineProps({
    columns: Array,
    data: Object,
    searchValue: String,
    title: String
});

const search = ref(props.searchValue);

const searchData = () => {
    router.get(route('iad.special.dti.viewDtiGc'), {
        search: search.value
    }, {
        preserveState: true
    });
}
</script>
<template>
    <AuthenticatedLayout>

        <Head :title="title" />
        <a-breadcrumb>
            <a-breadcrumb-item><a :href="route('iad.dashboard')">Home</a></a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>
        <a-card class="mt-5">
            <div class="flex justify-end mb-5">
                <a-input-search v-model:value="search" @change="searchData" enter-button allow-clear
                    placeholder="Input search here..." class="w-1/4" />
            </div>
            <a-table :columns="props.columns" :data-source="props.data.data" :pagination="false" size="small">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'action'">
                        <a-button type="primary" @click="() => $inertia.get(route('iad.special.dti.approvedDtiGc'), {
                            id: record.id,
                        })">
                            <PicLeftOutlined /> View
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="props.data" class="mt-5" />
        </a-card>
        <!-- {{ data }} -->
    </AuthenticatedLayout>
</template>
