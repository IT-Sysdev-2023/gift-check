<template>

  <Head title="Add New Promo" />
  <a-card title="Add New Promo"></a-card>
  <a-row :gutter="[16, 16]" class="mt-5">
    <a-col :span="12">
      <a-card>
        <a-form @submit="handleSubmit">
          <a-form-item label="Promo No:" name="promoNo">
            <a-input style="width: 6rem;" v-model:value="form.promoNo" readonly />
          </a-form-item>
          <a-form-item label="Date Created" name="dateCreated">
            <a-date-picker v-model:value="form.dateCreated" disabled />
          </a-form-item>
          <a-form-item label="Draw Date" name="drawDate">
            <a-date-picker :disabled-date="disabledDate" v-model:value="form.drawDate" />
          </a-form-item>
          <a-form-item label="Date Notified (Winners)" name="dateNotify">
            <a-date-picker :disabled-date="disabledDate" v-model:value="form.dateNotify" />
          </a-form-item>
          <a-form-item label="Expiration Date" name="expiryDate">
            <a-date-picker v-model:value="form.expiryDate" disabled />
          </a-form-item>
          <a-form-item label="Promo Group:" name="promoGroup">
            <a-select ref="select" placeholder="select" v-model:value="form.promoGroup" style="width: 120px">
              <a-select-option value="1">Group 1</a-select-option>
              <a-select-option value="2">Group 2</a-select-option>
            </a-select>
          </a-form-item>
          <a-form-item label="Promo Name:" name="promoName">
            <a-input v-model:value="form.promoName" />
          </a-form-item>
          <a-form-item label="Details:" name="details">
            <a-textarea v-model:value="form.details" allow-clear />
          </a-form-item>
          <a-form-item label="Prepared By:" name="prepby">
            <a-input v-model:value="$page.props.auth.user.full_name" readonly />
            <a-input v-model:value="form.prepby" class="hidden" />
          </a-form-item>
          <a-form-item>
            <a-button @click="addPromo()" type="primary" html-type="submit">Submit</a-button>
          </a-form-item>
        </a-form>
      </a-card>
    </a-col>
    <a-col :span="12">
      <a-card>
        <a-card title="" :bordered="false">
          <div>
            <a-table :dataSource="form.data" :columns="form.columns" :pagination="false" bordered>

              <template #bodyCell="{ column, record }">
                <template v-if="column.key === 'denom_id'">
                  <a-input readonly :value="record.countDen" style="width: 100px;"></a-input>
                </template>
              </template>
            </a-table>
          </div>
          <a-space class="mt-2">
            <div v-if="form.promoGroup !== null">
              <a-button @click="addGcModal(form.promoNo)" type="primary" ghost>
                <PlusOutlined /> Add GC
              </a-button>
            </div>
            <div v-else>
              <a-tooltip placement="topLeft" :arrow="mergedArrow">
                <template #title>
                  <span>Please Select Promo Group</span>
                </template>
                <a-button disabled type="primary" ghost>
                  <PlusOutlined /> Scan GC
                </a-button>
              </a-tooltip>
            </div>
            <a-button @click="scannedGc" type="primary" danger ghost>
              <BarcodeOutlined /> Scanned GC
            </a-button>
          </a-space>
        </a-card>
      </a-card>
    </a-col>
  </a-row>

  <a-modal v-model:open="open" width="50%" style="top: 65px" :title="title = 'GC Promo Validation '">
    <a-flex justify="space-between" align="middle" style="margin-top: 40px;">
      <a-form-item label="Promo No:" name="promoNo">
        <a-input style="width: 50px;" v-model:value="form.promoNo" readonly />
      </a-form-item>
      <a-form-item label="Date:" name="date">
        <a-date-picker v-model:value="form.dateCreated" readonly />
      </a-form-item>
      <a-form-item label="Group:" name="promoGroup">
        <a-input style="width: 35px;" v-model:value="form.promoGroup" readonly />
      </a-form-item>
      <a-form-item style="width: auto;" label="Scanned by:" name="scannedBy">
        <a-input style="width: auto;" v-model:value="form.prepByName" readonly />
      </a-form-item>
    </a-flex>
    <a-input v-model:value="form.barcode" @keyup.enter="validateGc" @input="updateDigitCount"
      style="height: 100px; font-size: 90px; " />
    <a-form-item class="mt-2" label="Input count:" name="promoGroup">
      <a-input style="width: 50px;" :value="digitCount" readonly />
    </a-form-item>
    <template #footer>
      <a-button key="back" @click="handleCancel()">Cancel</a-button>
      <a-button key="submit" type="primary" :disabled="!form.barcode" @click="validateGc()">
        OK
      </a-button>
    </template>
  </a-modal>

  <a-modal v-model:open="scannedbarcodemodal" title="Scanned GC" @ok="handleOk">

    <a-table :dataSource="form.scannedGc" :columns="form.scannedGccolumns" bordered>
      <template #bodyCell="{ column, record }">
        <div v-if="column.dataIndex === 'action'">
          <a-button @click="removeGc(record.tp_barcode)" danger>Remove</a-button>
        </div>
      </template>
    </a-table>
  </a-modal>

</template>

<script>
import { PlusOutlined, BarcodeOutlined, RedoOutlined } from "@ant-design/icons-vue";
import { Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import { notification } from 'ant-design-vue';
import axios from "axios";

export default {
  layout: AuthenticatedLayout,
  components: {
    PlusOutlined,
    BarcodeOutlined,
    RedoOutlined,
    Link
  },

  props: {
    PromoNum: String,
    promoId: Array,
    countItems: Array,
  },

  data() {
    return {
      scannedbarcodemodal: false,
      open: false,
      value: '',
      digitCount: 0,

      form: {
        promoNo: this.PromoNum,
        dateCreated: dayjs(),
        drawDate: null,
        dateNotify: null,
        expiryDate: null,
        promoGroup: null,
        promoName: null,
        details: null,
        prepby: this.$page.props.auth.user.user_id,
        prepByName: this.$page.props.auth.user.full_name,
        scannedGCValue: 0,
        barcode: '',
        data: [],
        columns: [],
        scannedGc: [],
        scannedGccolumns: []
      }
    };
  },


  watch: {
    'form.dateNotify'(newVal) {
      if (newVal) {
        this.form.expiryDate = dayjs(newVal).add(2, 'month');
      } else {
        this.form.expiryDate = null;
      }
    },
  },
  mounted() {
    this.fetch();
  },

  methods: {
    disabledDate(current) {
      return current && current < new Date().setHours(0, 0, 0, 0);
    },
    async fetch() {
      await axios.get(route('marketing.addPromo.get.denom')).then(response => {
        this.form.data = response.data.data;
        this.form.columns = response.data.columns;
      })
    },
    addGcModal() {
      this.open = true;
    },
    handleOk() {
      this.open = false;
    },
    handleCancel() {
      this.open = false;
    },
    updateDigitCount(event) {
      const input = event.target.value;
      this.digitCount = (input.match(/\d/g) || []).length;
    },
    validateGc() {
      axios.post(route('marketing.addPromo.gcpromovalidation'), {
        barcode: this.form.barcode,
        promoId: this.promoId,
        promoGroup: this.form.promoGroup,
        gctype: 1,
      }).then(response => {
        notification[response.data.response.type]({
          message: response.data.response.msg,
          description: response.data.response.description,
        });
        this.form.data = response.data.data;
        console.log(this.form.data)
        this.form.columns = response.data.columns
      })
    },

    scannedGc() {
      axios.get(route('marketing.addPromo.scannedGc'))
        .then(response => {
          this.scannedbarcodemodal = true;
          this.form.scannedGc = response.data.scannedGcdata;
          this.form.scannedGccolumns = response.data.scannedCol
        });
    },

    removeGc(data) {
      axios.post(route('marketing.addPromo.removeGc'), {
        barcode: data
      }).then(response => {
        notification[response.data.response.type]({
          message: response.data.response.msg,
          description: response.data.response.description,
        });
        this.form.data = response.data.data;
        this.form.scannedGc = response.data.dataScanned;
      })
    },
    addPromo() {
      axios.post(route('marketing.addPromo.newpromo'), {
        data: this.form
      }).then(response => {
        notification[response.data.response.type]({
          message: response.data.response.msg,
          description: response.data.response.description,
        });
        if (response.data.response.type === 'success') {
          window.location.href = "promo-list";
        }
      })
    }
  },

  beforeUnmount() {
    axios.post(route('marketing.addPromo.truncategcpromovalidation'));
  },


};
</script>
