import java.io.*;
import java.util.*;

public class getsys
{
	public static void main(String[] args)throws Exception
	{
		BufferedReader br = new BufferedReader(new FileReader("syscall-table.h"));
		PrintWriter pw = new PrintWriter(new BufferedWriter(new FileWriter("c-syscalls")));

		for(int i = 0; i < 348; i++)
		{
			String bj = br.readLine();
			String[] parts = bj.split(" ");
			String s = parts[7];
			int l = s.length();
			String str = s.substring(1,s.length()-1);
			pw.println(str);

		}
		br.close();
		pw.close();
	}
}
