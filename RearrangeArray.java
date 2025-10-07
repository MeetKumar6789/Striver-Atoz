import java.util.*;

public class RearrangeArray {
    public static int[] rearrangeArray(int[] nums) {
        List<Integer> pos = new ArrayList<>();
        List<Integer> neg = new ArrayList<>();

        for (int num : nums) {
            if (num > 0) pos.add(num);
            else neg.add(num);
        }

        int[] result = new int[nums.length];
        int i = 0, p = 0, n = 0;
        boolean positiveTurn = true;

        while (i < nums.length) {
            if (positiveTurn) {
                result[i++] = pos.get(p++);
            } else {
                result[i++] = neg.get(n++);
            }
            positiveTurn = !positiveTurn;
        }
        return result;
    }

    public static void main(String[] args) {
        int[] nums1 = {2, 4, 5, -1, -3, -4};
        System.out.println(Arrays.toString(rearrangeArray(nums1)));
        // Output: [2, -1, 4, -3, 5, -4]

        int[] nums2 = {1, -1, -3, -4, 2, 3};
        System.out.println(Arrays.toString(rearrangeArray(nums2)));
        // Output: [1, -1, 2, -3, 3, -4]
    }
}
