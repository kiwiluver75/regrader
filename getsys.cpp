#include <fstream>
#include <string>
#include <algorithm>
#include <boost/algorithm/string.hpp>

using namespace std;

int main()
{
	ifstream fin ("syscall-table.h");
	ofstream fout ("syscalls");
	for(int i = 0; i < 348; i++)
	{
		for(int j = 0; j < 7; j++)
		{
			string s; fin >> s;
		}
		string str;
		fin >> str;

		fout << str.substr(1, str.length()-3) << endl;

	}

	return 0;
}
