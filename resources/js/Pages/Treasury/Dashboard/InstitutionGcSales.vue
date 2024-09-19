<template>
    <!-- <Head :title="title" /> -->
    <a-breadcrumb style="margin: 15px 0">
        <a-breadcrumb-item>
            <Link :href="route('treasury.dashboard')">Home</Link>
        </a-breadcrumb-item>
        <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
    </a-breadcrumb>
    <a-card>
        <div class="flex justify-between mb-5">
            <div>
                <p>{{ date }}</p>
                <a-range-picker
                    v-model:value="dateRange"
                    @change="handleChangeDateRange"
                />
            </div>
            <div>
                <a-input-search
                    class="mr-1"
                    v-model:value="value"
                    placeholder="Search here..."
                    style="width: 300px"
                    @search="onSearch"
                />
                <a-button type="primary">
                    <template #icon>
                        <FileExcelOutlined />
                    </template>
                    Generate Excel Report
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

        <template #bodyCell="{ column, record }">
                <template v-if="column.key === 'customer'">
                    {{ record.institucustomer?.ins_name }}
                </template>
               
            </template>
        </a-table>
        <pagination-resource class="mt-5" :datarecords="data" />
    </a-card>
</template>
<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
export default {
    layout: AuthenticatedLayout,
    props: {
        data: Object,
        columns: Array,
        remainingBudget: Number,
        date: Array,
        title: String
    },
    data() {
        return {
            dateRange: this.dateRange
                ? [dayjs(this.date[0] ?? null), dayjs(this.date[1] ?? null)]
                : null,
        };
    },
    methods: {
        handleChangeDateRange(_, dateRange) {
            this.$inertia.get(
                route("budget.ledger"),
                { date: dateRange },
                {
                    preserveState: true,
                }
            );
        },
    },
};
</script>
