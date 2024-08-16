<template>
    <div class="flex justify-end mb-4">
        <a-button @click="() => $inertia.get(route('custodian.pendings.holder.entry'))">
            <template #icon>
                <RollbackOutlined />
            </template>
            Back to Pending Gc Holders
        </a-button>
    </div>
    <a-row :gutter="[16, 16]">
        <a-col :span="14">
            <special-gc-setup-details :record="record" />
        </a-col>
        <a-col :span="10">
            <a-card>
                <p class="mb-3">
                    <span class="" style="color: #3795BD;">
                        Entry By:
                    </span>
                    <a-typography-text keyboard>{{ $page.props.auth.user.full_name }}</a-typography-text>
                </p>
                <a-card size=small class=" mb-4">
                    <a-statistic title="Total Denomination" :value="record[0]?.total" :precision="2"
                        class="demo-class text-center" :value-style="{ color: '#cf1322' }">
                        <template #prefix>
                            <DollarCircleOutlined />
                        </template>
                    </a-statistic>
                </a-card>
                <a-row :gutter="[16, 16]" v-for="assign in record[0].special_external_gcrequest_items_has_many"
                    class="mb-4">
                    <a-col :span="8">
                        <span>Denomination</span>
                        <a-input prefix="#" :value="assign.specit_denoms" readonly />
                    </a-col>
                    <a-col :span="7">
                        <span>Quantity</span>
                        <a-input prefix="#" :value="assign.specit_qty" readonly />
                    </a-col>
                    <a-col :span="6">
                        <span>Holder</span>
                        <a-input prefix="#" readonly :value="checkExistence(assign.tempId)" />
                    </a-col>
                    <a-col :span="3">
                        <br>
                        <a-button @click="openDrawerAssign(assign)">
                            <template #icon>
                                <a-tooltip>
                                    <template #title>Assign Customer Employee</template>
                                    <UsergroupAddOutlined />
                                </a-tooltip>
                            </template>
                        </a-button>
                    </a-col>

                </a-row>
                <div class="flex justify-end ">
                    <a-button class="mt-4" type="primary" @click="submit" :disabled="!disableSubmit()">
                        <template #icon>
                            <FastForwardOutlined />
                        </template>
                        Continue Submitting Form
                    </a-button>
                </div>
            </a-card>
        </a-col>
    </a-row>
    <assign-customer-employee-gc v-model:open="assignDrawer" :selected="selected" @assign-temp="getAssinTemp" />

</template>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import pickBy from "lodash/pickBy";
import { notification } from 'ant-design-vue';
export default {
    layout: AuthenticatedLayout,
    props: {
        record: Object,
    },
    data() {
        return {
            assignDrawer: false,
            selected: {},
            assignTemp: []
        }
    },
    methods: {
        openDrawerAssign(data) {
            this.assignDrawer = true;
            this.selected = data;
        },
        getAssinTemp(data) {
            this.assignTemp = data;
        },
        checkExistence(temp) {
            return this.assignTemp?.filter((data) => data.trid == temp).length ?? 0;
        },
        submit() {
            this.$inertia.post(route('custodian.pendings.external.submit'), {
                data: { ...pickBy(this.assignTemp) },
                reqid: this.selected.specit_trid
            }, {
                onStart: () => {
                    this.isSubmitting = true;
                },
                onSuccess: (response) => {
                    notification[response.props.flash.status]({
                        message: response.props.flash.title,
                        description: response.props.flash.msg,
                    });
                },
                onError: () => {
                    this.isSubmitting = false;
                },
            });
        },
        disableSubmit() {
            return this.record[0].special_external_gcrequest_items_has_many.reduce((sum, item) => sum + item.specit_qty, 0)
                == this.assignTemp.length ?? 0;
        }

    }
}
</script>
