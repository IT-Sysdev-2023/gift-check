<template>
    <a-row :gutter="[16, 16]">
        <a-col :span="11">
            <a-card>
                <a-form layout="vertical">
                    <a-form-item>
                        <a-descriptions size="small" bordered>
                            <a-descriptions-item label="Prepared By:" class="text-end">{{
                                $page.props.auth.user.full_name
                                }}</a-descriptions-item>
                        </a-descriptions>
                        <a-descriptions size="small" bordered>
                            <a-descriptions-item label="Date Approved/Cancel:" class="text-end">{{ details.data[0].today
                                }}</a-descriptions-item>
                        </a-descriptions>
                    </a-form-item>
                    <a-form-item label="Request Status" v-model:value="form.status">
                        <a-select ref="select" placeholder="Select Status">
                            <a-select-option :value="1">Approved</a-select-option>
                            <a-select-option :value="2">Cancel</a-select-option>
                        </a-select>
                    </a-form-item>
                    <a-row :gutter="[16, 16]">
                        <a-col :span="12">
                            <a-form-item label="Check By:">
                                <a-select ref="select" v-model:value="form.checkby" placeholder="Select Checked by">
                                    <a-select-option v-for="item in cdata" :value="item.assig_id">{{ item.assig_name
                                        }}</a-select-option>
                                </a-select>
                            </a-form-item>
                        </a-col>
                        <a-col :span="12">
                            <a-form-item label="Approved By:">
                                <a-select ref="select" v-model:value="form.appby" placeholder="Select Approved By">
                                    <a-select-option v-for="item in cdata" :value="item.assig_id">{{ item.assig_name
                                        }}</a-select-option>
                                </a-select>
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-form-item>
                        <a-textarea v-model:value="value" placeholder="Basic usage" :rows="2" />
                    </a-form-item>
                    <a-form-item>
                        <a-upload-dragger v-model:fileList="fileList" name="file" :multiple="true"
                            action="https://www.mocky.io/v2/5cc8019d300000980a055e76" @change="handleChange"
                            @drop="handleDrop">
                            <p class="ant-upload-drag-icon">
                                <inbox-outlined></inbox-outlined>
                            </p>
                            <p class="ant-upload-text">Click or drag file to this area to upload</p>
                            <p class="ant-upload-hint">
                                Support for a single or bulk upload. Strictly prohibit from uploading company data or
                                other
                                band files
                            </p>
                        </a-upload-dragger> </a-form-item>
                    <a-form-item>
                        <a-button type="primary" block>
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
                        <a-descriptions-item label="RFPROM" span class="text-end">{{ details.data[0].pgcreq_reqnum
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="Department" class="text-end">{{ details.data[0].title
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="Retail Group" class="text-end">Group {{
                            details.data[0].pgcreq_group
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="Date Requested" class="text-end">{{
                            details.data[0].pgcreq_datereq
                            }}
                        </a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="Time Requested:" class="text-end">{{ details.data[0].time_req
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="Date Needed" class="text-end">{{ details.data[0].dateneeded
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="Total GC Budget:" class="text-end">{{ details.data[0].total
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="Remarks:" class="text-end">{{ details.data[0].remarks }}
                        </a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="Requested by:" class="text-end">{{ details.data[0].fullname
                            }}</a-descriptions-item>
                    </a-descriptions>
                </div>
            </a-card>
            <a-card class="mt-2">
                <a-table size="small" :pagination="false" class="mt-1" :columns="[
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

export default {
    props: {
        details: Object,
        denomination: Object,
    },
    data() {
        return {
            cdata: [],
            form: {
                appby: null,
                checkby: null,
                status: null,
            }
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
        }
    },
}
</script>
