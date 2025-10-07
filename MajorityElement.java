public class MajorityElement {
    public static int findMajorityElement(int[] nums) {
        int candidate = 0, count = 0;

        for (int num : nums) {
            if (count == 0) {
                candidate = num;
            }
            if (num == candidate) {
                count++;
            } else {
                count--;
            }
        }

        return candidate;
    }

    public static void main(String[] args) {
        int[] nums1 = {7, 0, 0, 1, 7, 7, 2, 7, 7};
        System.out.println("Output 1: " + findMajorityElement(nums1)); // 7

        int[] nums2 = {1, 1, 1, 2, 1, 2};
        System.out.println("Output 2: " + findMajorityElement(nums2)); // 1
    }
}
