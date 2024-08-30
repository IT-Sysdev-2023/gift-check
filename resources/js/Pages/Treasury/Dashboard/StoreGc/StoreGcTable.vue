<script setup>
import { highlighten } from "@/Mixin/UiUtilities";

const { highlightText } = highlighten();
</script>
<template>
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
                <a-range-picker v-model:value="form.date" />
            </div>
            <div>
                <a-input-search
                    class="mr-1"
                    v-model:value="form.search"
                    placeholder="Search here..."
                    style="width: 300px"
                />
            </div>
        </div>
        <a-table
            :data-source="data.data"
            :columns="columns"
            :loading="onLoading"
            bordered
            size="small"
            :pagination="false"
        >
            <template #title>
                <a-typography-title :level="4">{{ title }}</a-typography-title>
            </template>
            <template #bodyCell="{ column, record }">
                <template v-if="column.dataIndex">
                    <span
                        v-html="
                            highlightText(record[column.dataIndex], form.search)
                        "
                    >
                    </span>
                </template>
                <template v-if="column.key">
                    <span>
                        <!-- for the dynamic implementation of object properties, just add a key in column-->
                        {{ getValue(record, column.dataIndex) }}
                    </span>
                </template>

                <template v-if="column.dataIndex === 'action'">
                    <a-popconfirm
                        title="Are you sure allocate this request?"
                        ok-text="Yes"
                        cancel-text="No"
                        @confirm="confirm(record)"
                    >
                        <a-button
                            type="primary"
                            size="small"
                        >
                            <template #icon>
                                <FileSearchOutlined />
                            </template>
                            Confirm
                        </a-button>
                    </a-popconfirm>
                </template>
            </template>
        </a-table>

        <pagination-resource class="mt-5" :datarecords="data" />
    </a-card>
    <releasing-entry-modal v-model:open="openModal" :data="modalData"/>
</template>
<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import debounce from "lodash/debounce";
import pickBy from "lodash/pickBy";
import _ from "lodash";

export default {
    layout: AuthenticatedLayout,
    props: {
        title: String,
        data: Object,
        columns: Array,
        filters: Object,
    },
    data() {
        return {
            modalData: [],
            openModal: false,
            onLoading: false,
            form: {
                search: this.filters.search,
                date: this.filters.date
                    ? [dayjs(this.filters.date[0]), dayjs(this.filters.date[1])]
                    : [],
            },
            cancelledRecord: {},
        };
    },
    methods: {
        getValue(record, dataIndex) {
            return dataIndex.reduce((acc, index) => acc[index], record);
        },

        async confirm(record) {
            if(record.sgc_status != '2'){
                const {data} = await axios.get(route('treasury.store.gc.releasingEntry', record.sgc_id));
                this.modalData = data;
                this.openModal = true;
            }else{
                console.log('object');
            }
        },
    },

    watch: {
        form: {
            deep: true,
            handler: debounce(function () {
                const formattedDate = this.form.date
                    ? this.form.date.map((date) => date.format("YYYY-MM-DD"))
                    : [];

                this.$inertia.get(
                    route(route().current()),
                    { ...pickBy(this.form), date: formattedDate },
                    {
                        preserveState: true,
                    }
                );
            }, 600),
        },
    },
};
</script>
