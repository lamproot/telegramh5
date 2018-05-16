using System;
using System.Data;
using System.Configuration;
using System.Collections;
using System.IO;
using System.Net;
using System.Text;
using System.Web;
using System.Web.Security;
using System.Web.UI;
using System.Web.UI.WebControls;
using System.Web.UI.WebControls.WebParts;
using System.Web.UI.HtmlControls;
using System.Security.Cryptography.X509Certificates;
using System.Net.Security;

public partial class Post : System.Web.UI.Page
{
    public static string PostUrl = ConfigurationManager.AppSettings["WebReference.Service.PostUrl"];
    protected void Page_Load(object sender, EventArgs e)
    {

    }
    protected void ButSubmit_Click(object sender, EventArgs e)
    {
        string un = this.Txtaccount.Text.Trim();
        string pw = this.Txtpassword.Text.Trim();
        string mobile = this.Txtmobile.Text.Trim();
        string content = "【253云通讯】" +HttpContext.Current.Server.UrlEncode(this.Txtcontent.Text.Trim()) ;

     
        string postJsonTpl = "\"account\":\"{0}\",\"password\":\"{1}\",\"mobile\":\"{2}\",\"msg\":\"{3}\"";
        string jsonBody = string.Format(postJsonTpl, un, pw, mobile, content);
        string result = doPostMethodToObj("http://intapi.253.com/send/json", "{"+jsonBody+"}");
        LabelRetMsg.Text = result;
        
    }

    public static string doPostMethodToObj(string url, string jsonBody)
    {
        string result = String.Empty;
        HttpWebRequest httpWebRequest = (HttpWebRequest)WebRequest.Create(url);
        httpWebRequest.ContentType = "application/json";
        httpWebRequest.Method = "POST";

        // Create NetworkCredential Object 
        NetworkCredential admin_auth = new NetworkCredential("username", "password");

        // Set your HTTP credentials in your request header
        httpWebRequest.Credentials = admin_auth;

        // callback for handling server certificates
        ServicePointManager.ServerCertificateValidationCallback = delegate { return true; };

        using (StreamWriter streamWriter = new StreamWriter(httpWebRequest.GetRequestStream()))
        {
            streamWriter.Write(jsonBody);
            streamWriter.Flush();
            streamWriter.Close();
            HttpWebResponse httpResponse = (HttpWebResponse)httpWebRequest.GetResponse();
            using (StreamReader streamReader = new StreamReader(httpResponse.GetResponseStream()))
            {
                result = streamReader.ReadToEnd();
            }
        }
        return result;
    }

}
