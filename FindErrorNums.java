import java.util.*;

public class FindErrorNums {
    public static int[] findErrorNums(int[] nums) {
        int n = nums.length;
        Set<Integer> set = new HashSet<>();
        int duplicate = -1;
        long sum = 0;
        
        for (int num : nums) {
            if (set.contains(num)) duplicate = num;
            set.add(num);
            sum += num;
        }
        
        long expected = (long) n * (n + 1) / 2;
        int missing = (int)(expected - (sum - duplicate));
        
        return new int[]{duplicate, missing};
    }

    public static void main(String[] args) {
        int[] nums1 = {3, 5, 4, 1, 1};
        System.out.println(Arrays.toString(findErrorNums(nums1)));

        int[] nums2 = {1, 2, 3, 6, 7, 5, 7};
        System.out.println(Arrays.toString(findErrorNums(nums2)));
    }
}
