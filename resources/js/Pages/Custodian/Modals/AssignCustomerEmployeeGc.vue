<template>
    <a-modal title="Assign Customer Employee" style="top: 20px" :width="1200" :footer="false"
        :footer-style="{ textAlign: 'right' }" @close="onClose">

        <a-card>
            <a-row :gutter="[16, 16]">
                <a-col :span="12">
                    <a-form-item>
                        <a-typography-text keyboard>Last Name</a-typography-text><span
                            class="text-red-500 text-xs">*required</span>
                        <a-input v-model:value="form.lastname" allow-clear placeholder="Last Name">
                            <template #prefix>
                                <UserOutlined style="color: rgba(0, 0, 0, 0.25)" />
                            </template>
                        </a-input>
                    </a-form-item>
                    <a-form-item>
                        <a-typography-text keyboard>First Name</a-typography-text><span
                            class="text-red-500 text-xs">*required</span>
                        <a-input v-model:value="form.firstname" type="text" allow-clear placeholder="First Name">
                            <template #prefix>
                                <LockOutlined style="color: rgba(0, 0, 0, 0.25)" />
                            </template>
                        </a-input>
                    </a-form-item>
                    <a-form-item>
                        <a-typography-text keyboard>Middle Name</a-typography-text>
                        <a-input v-model:value="form.middlename" type="text" allow-clear placeholder="Middle Name">
                            <template #prefix>
                                <LockOutlined style="color: rgba(0, 0, 0, 0.25)" />
                            </template>
                        </a-input>
                    </a-form-item>
                    <a-form-item>
                        <a-typography-text keyboard>Suffix</a-typography-text>
                        <a-input v-model:value="form.suffix" type="text" allow-clear placeholder="Suffix">
                            <template #prefix>
                                <LockOutlined style="color: rgba(0, 0, 0, 0.25)" />
                            </template>
                        </a-input>
                    </a-form-item>
                </a-col>
                <a-col :span="12">
                    <a-form-item>
                        <a-typography-text keyboard>Address</a-typography-text>
                        <a-input v-model:value="form.address" type="text" allow-clear placeholder="Address">
                            <template #prefix>
                                <LockOutlined style="color: rgba(0, 0, 0, 0.25)" />
                            </template>
                        </a-input>
                    </a-form-item>
                    <a-form-item>
                        <a-typography-text keyboard>Voucher</a-typography-text>
                        <a-input v-model:value="form.voucher" type="text" allow-clear placeholder="Voucher">
                            <template #prefix>
                                <LockOutlined style="color: rgba(0, 0, 0, 0.25)" />
                            </template>
                        </a-input>
                    </a-form-item>
                    <a-form-item>
                        <a-typography-text keyboard>Business Unit</a-typography-text>
                        <a-select v-model:value="form.business" type="text" allow-clear placeholder="Business Unit"
                            ref="select" style="width: 100%" @change="handleChange">
                            <a-select-option v-for="(bu, key) in bunit" :key="key" :value="bu.name"> <template #prefix>
                                    <LockOutlined style="color: rgba(0, 0, 0, 0.25)" />
                                </template>{{ bu.name }}</a-select-option>
                        </a-select>
                    </a-form-item>

                </a-col>
            </a-row>
        </a-card>
        <a-card title="Details" class="mt-4" size="small">
            <a-table bordered size="small" :data-source="assignTemp.filter((data) => data.trid == selected.tempId)"
                :columns="columns">
            </a-table>
        </a-card>
        <a-form-item>
            <div class="flex justify-between mt-4">
                <a-button block class="mr-1" type="primary" danger @click="() => form.reset()">
                    Clear Form
                </a-button>
                <a-button block type="primary" @click="submit" :disabled="disableAssign()">
                    Assign
                </a-button>
            </div>
        </a-form-item>
    </a-modal>
</template>

<script>
import { useForm } from '@inertiajs/vue3';
import pickBy from "lodash/pickBy";
import { notification } from 'ant-design-vue';

export default {
    props: {
        selected: Object,
        bunit: Object,
    },
    data() {
        return {
            form: useForm({
                lastname: null,
                firstname: null,
                middlename: null,
                suffix: null,
                address: null,
                voucher: null,
                business: null,
            }),
            id: 1,
            assignTemp: [],
            columns: [
                {
                    title: 'Name',
                    dataIndex: 'firstname',
                    key: 'name',
                },
                {
                    title: 'Surname',
                    dataIndex: 'lastname',
                    key: 'age',
                },
                {
                    title: 'MI',
                    dataIndex: 'middlename',
                    key: 'address',
                },
                {
                    title: 'Suffix',
                    dataIndex: 'suffix',
                    key: 'address',
                },
                {
                    title: 'Address',
                    dataIndex: 'address',
                    key: 'address',
                },
                {
                    title: 'Voucher',
                    dataIndex: 'voucher',
                    key: 'address',
                },
                {
                    title: 'Busness Unit',
                    dataIndex: 'business',
                    key: 'address',
                },
            ],

        }
    },
    methods: {
        submit() {
            if (this.selected.specit_qty > this.assignTemp.filter((data) => data.trid == this.selected.tempId).length) {
                this.assignTemp.push({
                    id: this.id++,
                    trid: this.selected.tempId,
                    firstname: this.form.firstname,
                    lastname: this.form.lastname,
                    middlename: this.form.middlename,
                    suffix: this.form.suffix,
                    address: this.form.address,
                    voucher: this.form.voucher,
                    business: this.form.business,
                    denom: this.selected.specit_denoms,
                    reqid: this.selected.specit_trid
                })

                notification['success']({
                    message: 'Success',
                    description:
                        'Successfully Assigning Customer Employee!',
                    placement: 'topLeft'
                });
                this.$emit('assignTemp', this.assignTemp);
            } else {

                const placement = 'bottomRight';

                notification['warning']({
                    message: 'Maximum Limit',
                    description: 'Maximum Limit Reach For Assigning Customer Employee!',
                    placement: 'topLeft',
                });
            }
        },
        disableAssign() {
            return ((this.form.lastname == null || this.form.lastname == '') ||
                (this.form.firstname == null || this.form.firstname == '')
            ) ? true : false;

        }
    },

}
</script>
