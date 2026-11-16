(function () {
  "use strict";
  var form = document.getElementById("contactForm");
  if (!form) return;
  var select = document.getElementById("personne");
  var mail = document.getElementById("mailContact");
  var success = document.getElementById("formSuccess");
  var mailLink = document.getElementById("mailLink");

  select.addEventListener("change", function () {
    var opt = this.options[this.selectedIndex];
    mail.value = opt.dataset.mail || "";
  });

  function clean(text) {
    if (!text) return "Néant";
    text = text.trim();
    return text ? text.charAt(0).toUpperCase() + text.slice(1) : "Néant";
  }

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    if (!select.value && select.selectedIndex === 0) {
      select.focus();
      alert("Merci de choisir votre interlocuteur.");
      return;
    }
    if (!window.jspdf) { alert("Le générateur PDF n’est pas encore chargé, merci de réessayer dans un instant."); return; }

    var pdf = new window.jspdf.jsPDF();
    var y = 22;
    pdf.setFont("times", "bold"); pdf.setFontSize(17); pdf.setTextColor(110, 18, 64);
    pdf.text("CABINET LEVI EGEA & ASSOCIÉS", 20, y);
    pdf.setFont("helvetica", "normal"); pdf.setFontSize(9); pdf.setTextColor(110, 101, 105);
    y += 6; pdf.text("20, rue Michelet – 82000 Montauban – 05 63 20 00 61", 20, y);
    y += 4; pdf.setDrawColor(184, 149, 90); pdf.line(20, y, 190, y);
    y += 10; pdf.setTextColor(31, 26, 28); pdf.setFontSize(11);

    var dest = select.options[select.selectedIndex].text.trim();
    pdf.setFont("helvetica", "bold"); pdf.text("Destinataire : ", 20, y);
    pdf.setFont("helvetica", "normal"); pdf.text(dest + " (" + (mail.value || "Néant") + ")", 48, y);
    y += 10;

    var section = "";
    form.querySelectorAll("fieldset").forEach(function (fs, i) {
      if (i === 0) return;
      var title = fs.querySelector("legend").textContent;
      if (y > 260) { pdf.addPage(); y = 20; }
      pdf.setFont("times", "bold"); pdf.setFontSize(13); pdf.setTextColor(110, 18, 64);
      pdf.text(title, 20, y); y += 7;
      pdf.setFontSize(10.5); pdf.setTextColor(31, 26, 28);
      fs.querySelectorAll("input, textarea").forEach(function (field) {
        var label = field.name;
        var lines = pdf.splitTextToSize(label + " : " + clean(field.value), 170);
        if (y + lines.length * 6 > 280) { pdf.addPage(); y = 20; }
        pdf.setFont("helvetica", "normal");
        pdf.text(lines, 20, y);
        y += lines.length * 6;
      });
      y += 5;
    });

    var nom = (form.querySelector('input[name="Nom"]').value || "client").trim().replace(/\s+/g, "_");
    pdf.save("Dossier_" + nom + ".pdf");

    var subject = encodeURIComponent("Premier contact – " + (form.querySelector('input[name="Prénom"]').value + " " + form.querySelector('input[name="Nom"]').value).trim());
    var body = encodeURIComponent("Madame, Monsieur,\n\nVeuillez trouver ci-joint ma fiche de premier contact.\n\nCordialement");
    mailLink.href = "mailto:" + (mail.value || "cabinet@slea-avocats.fr") + "?subject=" + subject + "&body=" + body;
    success.classList.add("show");
    success.scrollIntoView({ behavior: "smooth", block: "center" });
  });
})();
