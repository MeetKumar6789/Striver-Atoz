public class ReversePairs {
    public static int mergeAndCount(int[] nums, int l, int m, int r) {
        int count = 0;
        int j = m + 1;

        for (int i = l; i <= m; i++) {
            while (j <= r && (long) nums[i] > 2L * nums[j]) {
                j++;
            }
            count += (j - (m + 1));
        }

        int[] temp = new int[r - l + 1];
        int i = l; 
        j = m + 1; 
        int k = 0;

        while (i <= m && j <= r) {
            if (nums[i] <= nums[j]) {
                temp[k++] = nums[i++];
            } else {
                temp[k++] = nums[j++];
            }
        }
        while (i <= m) temp[k++] = nums[i++];
        while (j <= r) temp[k++] = nums[j++];
        
        System.arraycopy(temp, 0, nums, l, temp.length);

        return count;
    }

    public static int mergeSortAndCount(int[] nums, int l, int r) {
        if (l >= r) return 0;
        int m = (l + r) / 2;
        int count = mergeSortAndCount(nums, l, m);
        count += mergeSortAndCount(nums, m + 1, r);
        count += mergeAndCount(nums, l, m, r);
        return count;
    }

    public static int reversePairs(int[] nums) {
        return mergeSortAndCount(nums, 0, nums.length - 1);
    }

    public static void main(String[] args) {
        int[] nums1 = {6, 4, 1, 2, 7};
        System.out.println(reversePairs(nums1)); // 3

        int[] nums2 = {5, 4, 4, 3, 3};
        System.out.println(reversePairs(nums2)); // 0
    }
}
