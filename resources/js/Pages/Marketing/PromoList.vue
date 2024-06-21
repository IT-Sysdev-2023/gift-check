<template>
    <Head title="Promo List" />
    <a-card>
        <a-card class="mb-2" title="Promo List"></a-card>
        <div class="flex justify-end">
            <a-input-search
                class="mt-5 mb-5"
                v-model:value="form.search"
                placeholder="input search text here."
                style="width: 300px"
                @search="onSearch"
            />
        </div>
        <a-table
            :dataSource="data.data"
            size="small"
            bordered
            :columns="columns"
            :pagination="false"
        >
            <template #bodyCell="{ column, record }">
                <template v-if="column.dataIndex == 'fullname'">
                    <span>{{ record.user.full_name }}</span>
                </template>
                <template v-if="column.dataIndex == 'View'">
                    <a-button @click="viewDetails(record)">
                        <template #icon>
                            <EyeOutlined />
                        </template>
                    </a-button>
                </template>
            </template>
        </a-table>
        <pagination-resource class="mt-5" :datarecords="data" />
    </a-card>
    <a-modal :footer="false" v-model:open="openViewModal">
        <a-row :gutter="[16, 16]">
            <a-col :span="8">
                <a-list>
                    <a-list-item>
                        <a-list-item-meta :description="selectedData.promo_id">
                            <template #title>
                                <a>Promo No.</a>
                            </template>
                        </a-list-item-meta>
                    </a-list-item>
                    <a-list-item>
                        <a-list-item-meta
                            :description="selectedData.promo_date"
                        >
                            <template #title>
                                <a>Date Created</a>
                            </template>
                        </a-list-item-meta>
                    </a-list-item>
                    <a-list-item>
                        <a-list-item-meta
                            :description="selectedData.promo_drawdate"
                        >
                            <template #title>
                                <a>Date Drawn</a>
                            </template>
                        </a-list-item-meta>
                    </a-list-item>
                    <a-list-item>
                        <a-list-item-meta
                            :description="selectedData.promo_datenotified"
                        >
                            <template #title>
                                <a>Date Notified</a>
                            </template>
                        </a-list-item-meta>
                    </a-list-item>
                    <a-list-item>
                        <a-list-item-meta
                            :description="selectedData.promo_dateexpire"
                        >
                            <template #title>
                                <a>Expiration Date</a>
                            </template>
                        </a-list-item-meta>
                    </a-list-item>
                    <a-list-item>
                        <a-list-item-meta
                            :description="selectedData.promo_group"
                        >
                            <template #title>
                                <a>Group</a>
                            </template>
                        </a-list-item-meta>
                    </a-list-item>
                    <a-list-item>
                        <a-list-item-meta
                            :description="selectedData.promo_name"
                        >
                            <template #title>
                                <a>Promo Name</a>
                            </template>
                        </a-list-item-meta>
                    </a-list-item>
                    <a-list-item>
                        <a-list-item-meta
                            :description="selectedData.promo_remarks"
                        >
                            <template #title>
                                <a>Details</a>
                            </template>
                        </a-list-item-meta>
                    </a-list-item>
                    <a-list-item>
                        <a-list-item-meta :description="selectedData.fullname">
                            <template #title>
                                <a>Created By</a>
                            </template>
                        </a-list-item-meta>
                    </a-list-item>
                </a-list>
            </a-col>
            <a-col :span="16">
                <a-input-search
                    class="mt-5 mb-5"
                    v-model:value="search"
                    placeholder="input search text here."
                    style="width: 300px"
                />
                <a-table
                    size="small"
                    :data-source="selectedViewDetails"
                    :columns="viewDetailsColumn"
                >
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.dataIndex == 'subviewinfo'">
                            <a-button
                                @click="viewSubDetails(record)"
                                v-if="record.vs_barcode !== null"
                            >
                                <template #icon>
                                    <EyeOutlined />
                                </template>
                            </a-button>
                        </template>
                    </template>
                </a-table>
            </a-col>
        </a-row>
        <a-modal
            v-model:open="openSubviewModal"
            width="60%"
            style="top: 65px"
            title="GC Verification Details"
            :footer="false"
        >
            <a-list>
                <a-list-item>
                    <a-list-item-meta
                        :description="selectedSubViewDetails[0]?.vs_date"
                    >
                        <template #title>
                            <a>Date</a>
                        </template>
                    </a-list-item-meta>
                    <a-list-item-meta
                        :description="selectedSubViewDetails[0]?.vs_time"
                    >
                        <template #title>
                            <a>Time</a>
                        </template>
                    </a-list-item-meta>
                </a-list-item>
                <a-list-item>
                    <a-list-item-meta
                        :description="selectedSubViewDetails[0]?.store_name"
                    >
                        <template #title>
                            <a>Store</a>
                        </template>
                    </a-list-item-meta>
                    <a-list-item-meta
                        :description="selectedSubViewDetails[0]?.cus_address"
                    >
                        <template #title>
                            <a>Address</a>
                        </template>
                    </a-list-item-meta>
                </a-list-item>
                <a-list-item>
                    <a-list-item-meta
                        :description="
                            capitalizeWords(
                                selectedSubViewDetails[0]?.firstname +
                                    ' ' +
                                    selectedSubViewDetails[0]?.lastname
                            )
                        "
                    >
                        <template #title>
                            <a>Verified By</a>
                        </template>
                    </a-list-item-meta>
                    <a-list-item-meta
                        :description="selectedSubViewDetails[0]?.cus_mobile"
                    >
                        <template #title>
                            <a>Mobile #</a>
                        </template>
                    </a-list-item-meta>
                </a-list-item>
                <a-list-item>
                    <a-list-item-meta
                        :description="
                            selectedSubViewDetails[0]?.vs_tf_denomination
                        "
                    >
                        <template #title>
                            <a>Denomination</a>
                        </template>
                    </a-list-item-meta>
                    <a-list-item-meta
                        :description="
                            selectedSubViewDetails[0]?.cus_fname +
                            ' ' +
                            selectedSubViewDetails[0]?.cus_lname
                        "
                    >
                        <template #title>
                            <a>Customer Name</a>
                        </template>
                    </a-list-item-meta>
                </a-list-item>
                <a-list-item>
                    <a-list-item-meta
                        :description="selectedSubViewDetails[0]?.vs_tf_balance"
                    >
                        <template #title>
                            <a>Balance</a>
                        </template>
                    </a-list-item-meta>
                    <a-list-item-meta
                        :description="
                            selectedSubViewDetails[0]?.vs_tf_purchasecredit
                        "
                    >
                        <template #title>
                            <a>Purchase Credit</a>
                        </template>
                    </a-list-item-meta>
                </a-list-item>
            </a-list>
        </a-modal>
    </a-modal>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import debounce from "lodash/debounce";
import axios from "axios";

export default {
    layout: AuthenticatedLayout,
    props: {
        data: Object,
        columns: Array,
    },
    data() {
        return {
            search: "",
            form: {
                search: "",
            },
            openViewModal: false,
            openSubviewModal: false,
            selectedData: [],
            key: "1",
            selectedViewDetails: [],
            selectedSubViewDetails: {},
            viewDetailsColumn: [
                {
                    title: "GC Barcode#",
                    dataIndex: "prom_barcode",
                },
                {
                    title: "Denomination.",
                    dataIndex: "denomination",
                },
                {
                    title: "GC Type",
                    dataIndex: "gctype",
                },
                {
                    title: "View Info",
                    dataIndex: "subviewinfo",
                },
            ],
        };
    },
    methods: {
        viewDetails(data) {
            this.selectedData = data;
            axios
                .get(route("get.view.details"), {
                    params: {
                        id: data.promo_id,
                    },
                })
                .then((response) => {
                    this.openViewModal = true;
                    this.selectedViewDetails = response.data.data;
                });
        },
        viewSubDetails(data) {
            axios
                .get(route("get.sub.barcode.details"), {
                    params: {
                        id: data.prom_barcode,
                    },
                })
                .then((response) => {
                    this.openSubviewModal = true;
                    this.selectedSubViewDetails = response.data;
                });
        },
        capitalizeWords(str) {
            return str.replace(/\b\w/g, (char) => char.toUpperCase());
        },
    },
    watch: {
        search: {
            deep: true,
            handler: debounce(function () {
                axios
                    .get(route("get.view.details"), {
                        params: {
                            search: this.search,
                            id: this.selectedData.promo_id,
                        },
                    })
                    .then((response) => {
                        this.selectedViewDetails = response.data.data;
                    });
            }, 600),
        },
        "form.search": {
            handler: debounce(function () {
                this.$inertia.get(
                    route("marketing.promo.list"),
                    {
                        search: this.form.search,
                    },
                    {
                        preserveState: true,
                    }
                );
            }, 600),
        },
    },
};
</script>
