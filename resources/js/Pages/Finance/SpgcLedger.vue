<template>
    <a-card>
        <ProgressBar :progressBar="progressBar" v-if="isGenerating"/>
        <div class="flex justify-between mb-5">
            <div>
                <a-range-picker v-model:value="form.date" />
            </div>
            <div>
                <a-input-search v-model:value="form.search" class="mr-1" placeholder="Search here..."
                    style="width: 300px" />
                <a-button type="primary" @click="start" :disabled="data.data.length <= 0">
                    <template #icon>
                        <FileExcelOutlined />
                    </template>
                    Export to Excel Reports
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
import { highlighten } from "@/Mixin/UiUtilities";
import ProgressBar from "@/Components/Finance/ProgressBar.vue";

export default {
    layout: AuthenticatedLayout,
    data() {
        return {
            form: {
                search: this.filters.search,
                date: this.filters.date
                    ? [dayjs(this.filters.date[0]), dayjs(this.filters.date[1])]
                    : [],
            },
            isGenerating: false,
            progressBar: {
                percentage: 0,
                message: "",
                totalRows: 0,
                currentRow: 0,
            },
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
    methods: {
        start() {
            this.$inertia.get(route('finance.spgc.ledger.start'), {
                date: this.filters.date ? [dayjs(this.filters.date[0]).format('YYYY-MM-DD'), dayjs(this.filters.date[1]).format('YYYY-MM-DD')]
                    : []
            });
        }
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                const formattedDate = this.form.date ? this.form.date.map((date) => date.format("YYYY-MM-DD")) : [];

                this.$inertia.get(route("finance.spgc.ledger"),
                    { ...pickBy(this.form), date: formattedDate },
                    {
                        preserveState: true,
                    }
                );
            }, 150),
        },
    },
    mounted() {
        this.$ws.private(`spgc-ledger-excel.${this.$page.props.auth.user.user_id}`)
            .listen(".generate-excel-spgc-ledger", (e) => {
                this.progressBar = e;
                this.isGenerating = true;
            });
    }
}
</script>
