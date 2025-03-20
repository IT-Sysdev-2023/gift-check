<template>
    <!-- adding something new hello world  -->

    <Head title="Dashboard" />
    <div>
        <a-row :gutter="[16, 16]">
            <a-col :span="10">
                <strong class="ml-1">
                    <a-typography-text keyboard>Enter Barcode</a-typography-text>
                </strong>
                <a-input-number class="p-2 pt-3 pb-3 text-2xl" style="width: 100%;" showCount @change="removeSpaces"
                    placeholder="Enter Barcode No" size="large" @keyup.enter="viewStatus1"
                    v-model:value="form.barcode" />
                <div v-if="isFetching">
                    <a-card class="mt-3" v-if="success">
                        <a-typography-title class="text-center" :level="4">{{ transType }}</a-typography-title>
                    </a-card>
                </div>
            </a-col>
            <a-col :span="14">
                <div v-if="isFetching">
                    <div v-if="statusBarcode" class="mb-2">
                        <a-alert message="Not Found!" class="mb-2" type="error" show-icon>
                            <template #description>
                                <p>
                                    Barcode # <strong>{{ barcode }}</strong> not found or Do not Exists
                                </p>
                            </template>
                        </a-alert>
                        <a-result status="404" title="Oppss 404" sub-title="Barcode not found">
                            <template #extra>
                                <a-typography-text code>Please Scan Another Barcode!</a-typography-text>
                            </template>
                        </a-result>
                    </div>
                    <div v-else-if="success">
                        <a-alert message="Success!" class="mb-2" type="success" show-icon>
                            <template #description>
                                <p>
                                    Barcode # <strong>{{ barcode }}</strong> found
                                </p>
                            </template>
                        </a-alert>
                    </div>
                    <div v-else-if="empty || !isFetching">
                        <a-result title="Status result here" sub-title="This where the status headed">
                            <template #icon>
                                <smile-twoTone />
                            </template>
                            <template #extra>
                                <a-typography-text code>Please Scan Barcode to View Statuses in Gift
                                    Check</a-typography-text>
                            </template>
                        </a-result>
                    </div>
                    <a-card class="" v-if="success">
                        <a-steps direction="vertical" style="color: green" :current="data.length - 1" :items="data">
                        </a-steps>
                    </a-card>
                </div>
            </a-col>
        </a-row>
    </div>

</template>
<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
    layout: AuthenticatedLayout,
    props: {
        data: Object,
        latestStatus: Boolean,
        transType: String,
        statusBarcode: String,
        empty: String || null,
        statusbar: Boolean,
        success: Boolean,
        barcode: Number,
        fetch: Boolean
    },

    data() {
        return {
            isFetching: this.fetch,
            isLoading: false,
            form: {
                barcode: this.barcode,
            },

        }
    },
    methods: {
        viewStatus1() {
            const barcode = this.form.barcode;
            this.isLoading = true;

            this.$inertia.get(route("admin.status.scanner"), {
                barcode,
                fetch: true,

            }, {
                onSuccess: () => {
                    this.isFetching = true;
                    this.isLoading = false;

                },
                preserveState: true
            });
        },
    },

}
</script>
