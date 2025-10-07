public class MaxProductSubarray {
    public static int maxProduct(int[] nums) {
        int maxProd = nums[0], minProd = nums[0], result = nums[0];
        for (int i = 1; i < nums.length; i++) {
            int tempMax = maxProd;
            maxProd = Math.max(Math.max(nums[i], maxProd * nums[i]), minProd * nums[i]);
            minProd = Math.min(Math.min(nums[i], tempMax * nums[i]), minProd * nums[i]);
            result = Math.max(result, maxProd);
        }
        return result;
    }

    public static void main(String[] args) {
        int[] nums1 = {4, 5, 3, 7, 1, 2};
        System.out.println(maxProduct(nums1)); // 840

        int[] nums2 = {-5, 0, -2};
        System.out.println(maxProduct(nums2)); // 0
    }
}
