public class FirstOccurrence {
    public static int findFirstIndex(int[] nums, int target) {
        for (int i = 0; i < nums.length; i++) {
            if (nums[i] == target) {
                return i; // return the first index where target is found
            }
        }
        return -1; // target not found
    }

    public static void main(String[] args) {
        int[] nums1 = {2, 3, 4, 5, 3};
        int target1 = 3;
        System.out.println("Output 1: " + findFirstIndex(nums1, target1)); // Expected 1

        int[] nums2 = {2, -4, 4, 0, 10};
        int target2 = 6;
        System.out.println("Output 2: " + findFirstIndex(nums2, target2)); // Expected -1
    }
}
