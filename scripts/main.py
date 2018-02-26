#encoding:utf-8

import mysql
import datetime
import sys
import subprocess

default_encoding = 'utf-8'
if sys.getdefaultencoding() != default_encoding:
    reload(sys)
    sys.setdefaultencoding(default_encoding)

conn = mysql.db()

# usernubmer, mobile
def getmemberinfo(rank):
	flag = False
	sql = """
		select usernumber, mobile, psd1, psd2 from zx_member where rank > %s
	""" % (rank)
	result = conn.query(sql)
	if result:
		return result

	return flag

def main(type, value):

	if(type == 'user'){
		cmd1 = "python /var/www/member/scripts/manage_bonus.py %s" % (value)
		info1 = subprocess.Popen(cmd1, stdout = subprocess.PIPE, shell = True).communicate()[0].strip()
		if info1 == "ok":
	}

	if(type == 'rank'){
		userlist = getmemberinfo(value);

		for userlist
			cmd1 = "python /var/www/member/scripts/manage_bonus.py %s" % (userlist[i]['tkey'])
			info1 = subprocess.Popen(cmd1, stdout = subprocess.PIPE, shell = True).communicate()[0].strip()
			if info1 == "ok":
	}


if __name__ == '__main__':
	if len(sys.argv) >= 2:
		uid = sys.argv[1]
		main(type, value)