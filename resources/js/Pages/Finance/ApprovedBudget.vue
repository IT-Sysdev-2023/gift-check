<template>
    <AuthenticatedLayout>
        <a-card>
            <div class="flex justify-end">
                <a-button @click="() => $inertia.visit(route('finance.dashboard'))" class="mb-2">
                    <RollbackOutlined />
                    Back to Dashboard
                </a-button>
            </div>
            <a-table size="small" :data-source="record.data" :columns="columns" bordered :pagination="false">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key === 'viewing'">
                        <a-button @click="view(record.br_id)" class="mr-1">
                            <template #icon>
                                <EyeFilled />
                            </template>
                        </a-button>
                        <a-button @click="reprint(record.br_no)">
                            <template #icon>
                                <PrinterOutlined />
                            </template>
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination class="mt-5" :datarecords="record" />
            <a-budget-details-drawer v-model:open="drawer" :selected="viewSelected" />
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';
import { ref } from 'vue';
import { notification } from 'ant-design-vue';

defineProps({
    record: Object,
    columns: Array,
});

const drawer = ref(false);
const viewSelected = ref({});

const view = async (id) => {
    await axios.get(route('finance.budget.approved.details', id)).then(
        res => {
            viewSelected.value = res.data;
            drawer.value = true;
        }
    )
}
const reprint = (id) => {
    fetch(`/finance/reprint-${id}`)
        .then(response => {
            if (!response.ok) {
                return response.json().then(errorData => {
                    // console.log(errorData.status)
                    notification[errorData.status]({
                        message: errorData.title,
                        description: errorData.msg,
                    });
                });
            } else {
                // Handle successful file download
                window.location.href = `/finance/reprint-${id}`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your request.');
        });
};

</script>
