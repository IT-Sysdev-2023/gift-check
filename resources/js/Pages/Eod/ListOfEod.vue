<template>
    <AuthenticatedLayout>
        <a-card>
            <a-table
                size="small"
                bordered
                :data-source="record.data"
                :columns="columns"
                :pagination="false"
            >
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key === 'storename'">
                        <!-- {{  }} -->
                        <span v-if="record.storename == null">
                            All Stores
                        </span>
                    </template>
                    <template v-if="column.key === 'view'">
                        <a-button @click="view(record.steod_id)">
                            <template #icon>
                                <EyeOutlined />
                            </template>
                            View
                        </a-button>
                    </template>
                </template>
            </a-table>

            <pagination :datarecords="record" class="mt-5" />
        </a-card>

        <!-- <td><?php echo is_null($v->vs_reverifydate) ?
            '<span class="label label-success">verified</span>' :
            <span class="label label-primary">reverified</span>';  ?></td> -->
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/core";
import { useForm } from "laravel-precognition-vue";
import { ref } from "vue";

defineProps({
    record: Object,
});


const columns = ref([
    {
        title: "Stores",
        dataIndex: "storename",
        key: "storename",
    },
    {
        title: "Eod By",
        dataIndex: "fullname",
    },
    {
        title: "Date",
        dataIndex: "date",
    },
    {
        title: "Time",
        dataIndex: "time",
    },
    {
        title: "View",
        key: "view",
        width: "12%",
        align: "center",
    },
]);
const view = (steod_id: number) => {
    router.get(route('eod.store.view', steod_id));
}
</script>
