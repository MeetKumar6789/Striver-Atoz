public class MissingNumber {
    public static int findMissingNumber(int[] nums) {
        int n = nums.length;
        int expectedSum = n * (n + 1) / 2;
        int actualSum = 0;
        for (int num : nums) {
            actualSum += num;
        }
        return expectedSum - actualSum;
    }

    public static void main(String[] args) {
        int[] nums1 = {0, 2, 3, 1, 4};
        System.out.println("Output 1: " + findMissingNumber(nums1)); // 5

        int[] nums2 = {0, 1, 2, 4, 5, 6};
        System.out.println("Output 2: " + findMissingNumber(nums2)); // 3
    }
}
