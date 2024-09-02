<template>
    <a-row :gutter="[16, 16]">
        <a-col :span="11">
            <a-card>
                <a-form layout="vertical">
                    <a-form-item>
                        <a-descriptions size="small" bordered>
                            <a-descriptions-item style="width: 50%;" label="Prepared By:" class="text-end">{{
                                $page.props.auth.user.full_name
                            }}</a-descriptions-item>
                        </a-descriptions>
                        <a-descriptions size="small" bordered>
                            <a-descriptions-item style="width: 50%;" label="Date Approved/Cancel:" class="text-end">{{
                                details.data[0].today
                                }}</a-descriptions-item>
                        </a-descriptions>
                    </a-form-item>

                    <a-form-item label="Request Status" has-feedback :validate-status="errors.status ? 'error' : ''"
                        :help="errors.status">
                        <a-select v-model:value="form.status" ref="select" placeholder="Select Status"
                            @change="() => errors.status = null">
                            <a-select-option :value="1">Approved</a-select-option>
                            <a-select-option :value="2">Cancel</a-select-option>
                        </a-select>
                    </a-form-item>
                    <a-row :gutter="[16, 16]">
                        <a-col :span="12">
                            <a-form-item label="Check By:" has-feedback :validate-status="errors.checkby ? 'error' : ''"
                                :help="errors.checkby">
                                <a-select ref="select" v-model:value="form.checkby" placeholder="Select Checked by"
                                    @change="() => errors.checkby = null">
                                    <a-select-option v-for="item in cdata" :value="item.assig_id">{{ item.assig_name
                                        }}</a-select-option>
                                </a-select>
                            </a-form-item>
                        </a-col>
                        <a-col :span="12">
                            <a-form-item label="Approved By:" has-feedback
                                :validate-status="errors.appby ? 'error' : ''" :help="errors.appby">
                                <a-select ref="select" v-model:value="form.appby" placeholder="Select Approved By"
                                    @change="() => errors.appby = null">
                                    <a-select-option v-for="item in cdata" :value="item.assig_id">{{ item.assig_name
                                        }}</a-select-option>
                                </a-select>
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-form-item has-feedback :help="errors.remarks" :validate-status="errors.remarks ? 'error' : ''">
                        <a-textarea v-model:value="form.remarks" placeholder="Enter remarks" :rows="2"
                            @change="() => errors.remarks = null" />
                    </a-form-item>
                    <a-divider>
                        <a-typography-text code>upload image</a-typography-text>
                    </a-divider>
                    <div class="flex justify-center">
                        <a-form-item>
                            <ant-upload-image @handle-change="handleImageChange" />
                        </a-form-item>
                    </div>
                    <a-form-item>
                        <a-button type="primary" block @click="approve" :loading="form.processing">
                            <template #icon>
                                <TagsFilled />
                            </template>
                            Submit Approval Form
                        </a-button>
                    </a-form-item>
                </a-form>
            </a-card>
        </a-col>
        <a-col :span="13">
            <a-card>
                <a-statistic :value="details.data[0].current" :precision="2" class="demo-class mb-5"
                    :value-style="{ color: '#3DC2EC' }">
                    <template #title>
                        <AreaChartOutlined /> Current Available Budget
                    </template>
                    <template #prefix>
                        <LikeOutlined />
                    </template>
                </a-statistic>
                <div class="flex justify-center">
                    <a-typography-text class="mb-1 " keyboard>Promo Gc Request Details</a-typography-text>
                </div>
                <div class="mt-2">
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="RFPROM" span style="width: 50%;">{{ details.data[0].pgcreq_reqnum
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="Department" style="width: 50%;">{{ details.data[0].title
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="Retail Group" style="width: 50%;">Group {{
                            details.data[0].pgcreq_group
                        }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="Date Requested" style="width: 50%;">{{
                            details.data[0].pgcreq_datereq
                        }}
                        </a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="Time Requested:" style="width: 50%;">{{ details.data[0].time_req
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="Date Needed" style="width: 50%;">{{ details.data[0].dateneeded
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="Total GC Budget:" style="width: 50%;">{{ details.data[0].total
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="Remarks:" style="width: 50%;">{{ details.data[0].remarks }}
                        </a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="Requested by:" style="width: 50%;">{{ details.data[0].fullname
                            }}</a-descriptions-item>
                    </a-descriptions>
                </div>
            </a-card>

            <a-card class="mt-2">
                <a-table size="small" bordered :pagination="false" class="mt-1" :columns="[
                    {
                        title: 'Denomination',
                        dataIndex: 'denomination',
                        key: 'denom',
                    },
                    {
                        title: 'Quantity',
                        dataIndex: 'pgcreqi_qty',
                        align: 'center'

                    },
                    {
                        title: 'Subtotal',
                        dataIndex: 'subtotal',
                        align: 'end'
                    },
                ]" :data-source="denomination.data">
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.key == 'denom'">
                            <span>
                                {{ record.denomination.denomination }}
                            </span>
                        </template>
                    </template>

                </a-table>
                <div class="mt-2 flex justify-end">
                    <a-typography-text class="mt-2" keyboard>Total: {{ denomination.total }} </a-typography-text>
                </div>
            </a-card>
        </a-col>
    </a-row>

</template>
<script>
import axios from 'axios';
import pickBy from "lodash/pickBy";
import { notification } from 'ant-design-vue';
import { useForm } from '@inertiajs/vue3';
export default {
    props: {
        details: Object,
        denomination: Object,
        reqid: Number,
    },
    data() {
        return {
            cdata: [],
            errors: {},
            form: useForm({
                appby: null,
                checkby: null,
                status: null,
                remarks: null,
                reqid: this.reqid,
                file: null,
            })
        }
    },
    created() {
        this.getCheckBy();
    },
    methods: {
        getCheckBy() {
            axios.get(route('search.checkBy'))
                .then(response => {
                    this.cdata = response.data;
                })
        },
        approve() {
            this.form.transform((data) => ({
                ...pickBy(data)
            })).post(route('finance.approve.request'), {

                onSuccess: (res) => {
                    notification[res.props.flash.status]({
                        message: res.props.flash.title,
                        description: res.props.flash.msg,
                    });
                },

                onError: (e) => {
                    this.errors = e;

                    notification['error']({
                        message: 'Required',
                        description: 'Some fields are I miss you',
                    });
                }
            })
        },
        handleImageChange(file) {
            this.form.file = file.file;
        }

    },
}
</script>
