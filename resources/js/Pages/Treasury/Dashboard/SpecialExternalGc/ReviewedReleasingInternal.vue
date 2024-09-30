<template>
    <AuthenticatedLayout>
        <Head :title="title" />
    <a-breadcrumb style="margin: 15px 0">
        <a-breadcrumb-item>
            <Link :href="route('treasury.dashboard')">Home</Link>
        </a-breadcrumb-item>
        <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
    </a-breadcrumb>
    <a-card>
        <div class="flex justify-between mb-5">
            <div>
                <!-- <a-range-picker v-model:value="form.date" /> -->
            </div>
            <div>
                <!-- <a-input-search
                    class="mr-1"
                    v-model:value="form.search"
                    placeholder="Search here..."
                    style="width: 300px"
                /> -->
            </div>
        </div>
        <a-table
            :data-source="records.data"
            :columns="columns"
            bordered
            size="small"
            :pagination="false"
        >
            <template #title>
                <a-typography-title :level="4">{{ title }}</a-typography-title>
            </template>
            <template #bodyCell="{ column, record }">
               
                <template v-if="column.key == 'approved'">
                    {{ record.approvedRequest.reqap_approvedby }}
                </template>
                <template v-if="column.key == 'customer'">
                    {{ record.specialExternalCustomer.spcus_acctname }}
                </template>
                <template v-if="column.key == 'reviewed'">
                    {{ record.reviewed }}
                </template>

                <!-- <template v-if="column.dataIndex === 'action'">
                    <a-button
                        type="primary"
                        size="small"
                        @click="viewRecord(record.br_id)"
                    >
                        <template #icon>
                            <FileSearchOutlined />
                        </template>
                        View
                    </a-button>
                </template> -->
            </template>
        </a-table>
        <!-- <a-modal v-model:open="showModal" width="1000px" :footer="null">
            <Description :data="descriptionRecord" />
        </a-modal> -->

        <pagination-resource class="mt-5" :datarecords="records" />
    </a-card>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineProps<{
    title: string,
    records: {
        data: any[]
    },
    columns: any[]
}>();

</script>

<style lang="scss" scoped></style>
