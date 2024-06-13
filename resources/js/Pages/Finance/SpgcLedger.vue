<template>
    <a-card>
        <div class="flex justify-between mb-5">
            <div>
                <a-range-picker v-model:value="form.date" />
            </div>
            <div>
                <a-input-search v-model:value="form.search" class="mr-1" placeholder="Search here..."
                    style="width: 300px" />
                <a-button type="primary">
                    <template #icon>
                        <FileExcelOutlined />
                    </template>
                    Export to Excel
                </a-button>
            </div>
        </div>
        <a-table :data-source="data.data" :columns="columns" bordered size="small" :pagination="false">
            <template #bodyCell="{ column, record }">
                <template v-if="column.dataIndex">
                    <span v-html="highlightText(record[column.dataIndex], form.search)
                        ">
                    </span>
                </template>
            </template>
        </a-table>
        <div class="flex justify-end p-2 mt-5">
            <p class="font-semibold text-gray-700">
                Spgc Ledgers:
            </p>
            &nbsp;
            <span>
                <a-tag color="blue" style="font-size: 13px; letter-spacing: 1px;">{{ operators
                    }}</a-tag>
            </span>

        </div>
        <pagination-resource class="mt-5" :datarecords="data" />
    </a-card>
</template>
<script>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import dayjs from "dayjs";
import { highlighten } from "@/Mixin/highlighten";

export default {
    layout: AuthenticatedLayout,
    data() {
        return {
            form: {
                search: this.filters.search,
                date: this.filters.date
                    ? [dayjs(this.filters.date[0]), dayjs(this.filters.date[1])]
                    : [],
            }
        }
    },

    setup() {
        const { highlightText } = highlighten();
        return { highlightText };
    },
    props: {
        data: Object,
        columns: Array,
        operators: Number,
        filters: Object
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                const formattedDate = this.form.date ? this.form.date.map((date) => date.format("YYYY-MM-DD")) : [];

                this.$inertia.get(
                    route("finance.spgc.ledger"),
                    { ...pickBy(this.form), date: formattedDate },
                    {
                        preserveState: true,
                    }
                );
            }, 150),
        },
    }
}
</script>
