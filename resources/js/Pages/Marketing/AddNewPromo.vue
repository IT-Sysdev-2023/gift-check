<script>
import { PlusOutlined, BarcodeOutlined } from "@ant-design/icons-vue";
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
  layout: AuthenticatedLayout,
  components: {
    PlusOutlined,
    BarcodeOutlined
  }
};
</script>

<template>
  <a-card title="Add New Promo"></a-card>
  <a-row :gutter="[16, 16]">
    <a-col :span="12">
      <a-card>
        <a-form>
          <a-form-item label="Promo No:" name="promoNo">
            <a-input style="width: 6rem;" v-model:value="promoNo" disabled />
          </a-form-item>
          <a-form-item label="Date Created" name="dateCreated">
            <a-date-picker v-model:value="dateCreated" disabled />
          </a-form-item>
          <a-form-item label="Draw Date" name="drawDate">
            <a-date-picker v-model:value="drawDate" />
          </a-form-item>
          <a-form-item label="Date Notified (Winners)" name="dateNotify">
            <a-date-picker v-model:value="dateNotify" />
          </a-form-item>
          <a-form-item label="Expiration Date" name="expiryDate">
            <a-date-picker v-model:value="expiryDate" />
          </a-form-item>
          <a-form-item label="Promo Group:" name="promoGroup">
            <a-select ref="select" placeholder="select" v-model:value="value1" style="width: 120px">
              <a-select-option value="Group1">Group 1</a-select-option>
              <a-select-option value="Group2">Group 2</a-select-option>
            </a-select>
          </a-form-item>
          <a-form-item label="Promo Name:" name="promoName">
            <a-input v-model:value="promoName" />
          </a-form-item>
          <a-form-item label="Details:" name="details">
            <a-textarea v-model:value="details" allow-clear />
          </a-form-item>
          <a-form-item label="Prepared By:" name="prepby">
            <a-input v-model:value="$page.props.auth.user.full_name" disabled />
          </a-form-item>
          <a-form-item>
            <a-button type="primary" html-type="submit">Submit</a-button>
          </a-form-item>
        </a-form>
      </a-card>
    </a-col>
    <a-col :span="12">
      <a-card>
        <a-card title="Denomination" :bordered="false">
          <a-form-item label="₱ 100.00" name="promoNo">
            <a-input type="number" v-model:value="promoNo" :min="0" />
          </a-form-item>
          <a-form-item label="₱ 500.00" name="promoNo">
            <a-input type="number" v-model:value="promoNo" :min="0" />
          </a-form-item>
          <a-form-item label="₱ 1,000.00" name="promoNo">
            <a-input type="number" v-model:value="promoNo" :min="0" />
          </a-form-item>
          <a-form-item label="₱ 2,000.00" name="promoNo">
            <a-input type="number" v-model:value="promoNo" :min="0" />
          </a-form-item>
          <a-form-item label="₱ 5,000.00" name="promoNo">
            <a-input type="number" v-model:value="promoNo" :min="0" />
          </a-form-item>
          <a-space>
            <a-button type="primary" ghost>
              <PlusOutlined /> Add GC
            </a-button>
            <a-button type="primary" danger ghost>
              <BarcodeOutlined />Scanned GC
            </a-button>
          </a-space>
        </a-card>
      </a-card>
    </a-col>
  </a-row>
</template>
