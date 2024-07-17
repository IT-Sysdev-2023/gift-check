<template>

    <Head title="Dashboard" />
    <div>
        <a-row :gutter="[16, 16]">
            <a-col :span="10">
                <a-card>
                    <a-alert :message="isLoading ? 'Scanning Barcode....': 'Scan Barcode here'" type="info" show-icon >

                    </a-alert>
                    <a-input @change="removeSpaces" placeholder="Enter Barcode No" size="large"
                        @keyup.enter="viewStatus1" v-model:value="form.barcode" />
                </a-card>
                <div v-if="isFetching">
                    <a-card class="mt-3" v-if="success">
                        <a-timeline>
                            <a-timeline-item>{{ transType }}</a-timeline-item>
                        </a-timeline>
                    </a-card>
                </div>
            </a-col>
            <a-col :span="14">
                <div v-if="isFetching">
                    <div v-if="statusBarcode" class="mb-2">
                        <a-result status="404" title="Oppss 404" sub-title="Barcode not found">
                            <template #extra>
                                <a-typography-text code>Please Scan Another Barcode!</a-typography-text>
                            </template>
                        </a-result>
                    </div>
                    <div v-else-if="success">
                        <a-alert message="Success!" class="mb-2" description="Barcode found!" type="success"
                            show-icon />
                    </div>
                    <div v-else-if="empty">
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
        empty: String,
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
            const barcode = this.form.barcode.replace(/\s+/g, '');
            this.isLoading = true;

            this.$inertia.get(route("admin.dashboard"), {
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
