<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
</script>

<template>

    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div>
            <div v-if="statusBarcode" class="mb-2">
                <a-alert message="Opps 404!" description="Barcode Not found!" type="error" show-icon />
            </div>
            <div v-else>
                <a-alert message="Success!" class="mb-2" description="Barcode found!" type="success" show-icon />
            </div>
            <a-row :gutter="[16, 16]">
                <a-col :span="10">
                    <a-card>
                        <a-tag color="processing" class="mb-3">
                            <InfoCircleOutlined /> Enter Barcode Here Or Scan
                            Barcode
                        </a-tag>
                        <a-input @change="removeSpaces" placeholder="Enter Barcode No" size="large"
                            v-model:value="form.barcode" />
                        <a-button @click="viewStatus1">View Status</a-button>
                    </a-card>
                    <div v-if="isFetching">
                        <a-card class="mt-3"  v-if="!statusBarcode">
                            <a-timeline>
                                <a-timeline-item>{{ transType }}</a-timeline-item>
                                <a-timeline-item>Solve initial network problems
                                    2015-09-01</a-timeline-item>

                            </a-timeline>
                        </a-card>
                    </div>
                </a-col>
                <a-col :span="14">
                    <div v-if="isFetching">
                        <a-card class="" v-if="!statusBarcode">
                            <a-steps direction="vertical" style="color: green" :current="data.length - 1" :items="data">
                            </a-steps>
                        </a-card>
                    </div>
                </a-col>
            </a-row>


        </div>
    </AuthenticatedLayout>
</template>
<script>

export default {
    props: {
        data: Object,
        latestStatus: Boolean,
        transType: String,
        statusBarcode: String,
    },

    data() {
        return {
            isFetching: false,
            isLoading: false,
            form: {
                barcode: null,
            },

        }
    },
    methods: {
        viewStatus1() {
            const barcode = this.form.barcode.replace(/\s+/g, '');
            this.isLoading = true;

            this.$inertia.get(route("admin.dashboard"), {
                barcode,
            }, {
                onSuccess: () => {
                    this.isFetching = true;
                    this.isLoading = false;
                },

                preserveState: true
            });
        },
    }
}
</script>
