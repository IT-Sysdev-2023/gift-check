<template>
    <a-card>
        <div class="flex justify-between mb-5">
            <div>
                <a-range-picker />
            </div>
            <div>
                <a-input-search
                    class="mr-1"
                    placeholder="Search here..."
                    style="width: 300px"
                />
                <a-button type="primary">
                    <template #icon>
                        <FileExcelOutlined />
                    </template>
                    Export to Excel
                </a-button>
            </div>
        </div>
        <a-table
            :data-source="data.data"
            :columns="columns"
            bordered
            size="small"
            :pagination="false"
        >
        </a-table>
        <pagination-resource class="mt-5" :datarecords="data" />
    </a-card>
</template>
<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
    layout: AuthenticatedLayout,
    props: {
        data: Object,
        columns: Array,
        remainingBudget: Number,
    },
};
</script>
